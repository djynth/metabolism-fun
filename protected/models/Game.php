<?php

/**
 * @db id           int(11)     a unique game ID
 * @db completed    tinyint(1)  whether the game has been finished
 * @db score        smallint(6) the number of points earned by the player
 * @db name         varchar(20) a non-unique user-chosen name for the game
 * @db turn         smallint(6) the current turn, i.e. the number of times the
 *                              player has made a move (indexed from 0)
 * @db user_id      int(11)     the user ID of the owner of this game, -1 if the
 *                              owner has not signed in
 * @db mode         int(11)     the mode of this game
 * @db challenge_id int(11)     the ID of the challenge dictating the parameters
 *                              of this game
 * @fk user      User
 * @fk state     array(GameState)
 * @fk challenge Challenge
 */
class Game extends CActiveRecord
{
    public $completed = 0;
    public $score = 0;
    public $name = "New Game";
    public $turn = 0;
    public $user_id = -1;
    public $max_turns = -1;
    public $mode = 1;
    
    const MODE_FREE_PLAY = 1;
    const MODE_CHALLENGE = 2;
    const MODE_CAMPAIGN = 3;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'games';
    }

    public function primaryKey()
    {
        return 'id';
    }

    public function relations()
    {
        return array(
            'user' => array(
                self::BELONGS_TO,
                'User',
                array('user_id' => 'id'),
            ),
            'state' => array(
                self::HAS_MANY,
                'GameState',
                array('id' => 'game_id'),
            ),
            'challenge' => array(
                self::BELONGS_TO,
                'Challenge',
                array('challenge_id' => 'id'),
            ),
        );
    }

    function __construct()
    {
        parent::__construct();

        if (($user = User::getCurrentUser()) !== null) {
            $this->user_id = $user->id;
        }
        $this->setState(Game::getInitialState());
        $this->save();

        $this->appendState();
    }

    public function save()
    {
        parent::save();

        $state = array();
        foreach (Resource::getAmounts() as $resource => $organs) {
            foreach ($organs as $organ => $amount) {
                $state[] = array(
                    'game_id' => $this->id,
                    'resource_id' => $resource,
                    'organ_id' => $organ,
                    'amount' => $amount,
                );
            }
        }

        foreach (Organ::getActionCounts() as $organ => $count) {
            $state[] = array(
                'game_id' => $this->id,
                'resource_id' => null,
                'organ_id' => $organ,
                'amount' => $count,
            );
        }

        Yii::app()->db->getCommandBuilder()->createMultipleInsertCommand(
            'game_state',
            $state
        )->execute();
    }

    public static function load($id)
    {
        $game = self::model()->findByPk($id);
        $amounts = array();
        $actionCounts = array();
        foreach ($game->state as $state) {
            if ($state->resource !== null) {
                $amounts[$state->resource->id][$state->organ->id] = 
                    $state->amount;
            } else {
                $actionCounts[$state->organ->id] = $state->amount;
            }
        }
        Resource::setAmounts($amounts);
        Organ::setActionCounts($actionCounts);
        $this->appendState();
        return $game;
    }

    public function getState()
    {
        $passivePathways = array();
        foreach (Pathway::getPassivePathways() as $pathway) {
            foreach ($pathway->organs as $organ) {
                $passivePathways[$pathway->id][$organ->id] = $pathway->canRun(
                    $pathway->passive,
                    $organ
                );
            }
        }

        return array(
            'score' => $this->score,
            'turn' => $this->turn,
            'max_turns' => $this->challenge->max_turns,
            'resources' => Resource::getAmounts(),
            'action_counts' => Organ::getActionCounts(),
            'completed' => $this->completed,
            'passive_pathways' => $passivePathways,
        );
    }

    public static function getInitialState()
    {
        $amounts = array();

        foreach (Resource::model()->findAll() as $resource) {
            $amounts[$resource->id] = array();
            foreach ($resource->organs as $organ) {
                $amounts[$resource->id][$organ->id] = 0;
            }
        }

        return array(
            'score' => 0,
            'turn' => 0,
            'max_turns' => 0,
            'resources' => $amounts,
            'action_counts' => Organ::getStartingActionCounts(),
            'completed' => false,
        );
    }

    public function setState($state)
    {
        $this->score = $state['score'];
        $this->setTurn($state['turn']);
        Resource::setAmounts($state['resources']);
        Organ::setActionCounts($state['action_counts']);
    }

    public function setTurn($turn)
    {
        $this->turn = $turn;
        if ($this->turn == self::MAX_TURNS) {
            $this->completed = true;
        }
    }

    public function onTurn($pathway, $organ, $times, $reverse)
    {
        if ($pathway->action) {
            $organ->setActionCount($organ->getActionCount() + $times);
        }

        $this->score += ($reverse ? -1 : 1) * $times * $pathway->points;
        if (!$pathway->passive) {
            $this->score -= Resource::getPenalizations();
            foreach (Pathway::getPassivePathways() as $pathway) {
                foreach ($pathway->organs as $organ) {
                    $pathway->run($this, $pathway->passive, $organ, false, true);    
                }
            }

            $this->setTurn($this->turn + 1);
            $this->appendState();
        }
    }

    private function appendState()
    {
        if (Yii::app()->session['states'] === null) {
            Yii::app()->session['states'] = array();
        }
        $states = Yii::app()->session['states'];
        $states[] = $this->getState();
        Yii::app()->session['states'] = $states;
    }

    public function undo()
    {
        $states = Yii::app()->session['states'];
        if (count($states) > 1) {
            array_pop($states);
            Yii::app()->session['states'] = $states;
            $this->setState($states[count($states) - 1]);
        }
    }
}