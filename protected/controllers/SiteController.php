<?php

class SiteController extends CController
{
    public function actionIndex()
    {
        Game::initGame();
        Resource::initStartingValues();

        $this->render('index');
    }

    public function actionResult()
    {
        $user = User::getCurrentUser();
        if ($user !== null || Game::isGameOver()) {
            $this->render('result', array(
                'game_id' => Game::getGameId()
            ));
        } elseif ($user !== null && !Game::isGameStarted()) {
            $lastGame = Game::model()->findByAttributes(array(''));
            // TODO view the last played game
        } elseif (isset($_GET['game'])) {
            // TODO view the game with id in GET if the current user is allowed to
        }
    }

    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            var_dump($error);
            die;
        }
    }

    public function actionPathway()
    {
        if (isset($_POST['pathway_id'], $_POST['organ'], $_POST['times'],
                  $_POST['reverse'])) {
            $pathway = Pathway::model()->findByPk($_POST['pathway_id']);
            $reverse = $_POST['reverse'] == 'true' ? true : false;

            $success = $pathway->run(
                $_POST['times'],
                Organ::model()->findByPk($_POST['organ']),
                null,
                $reverse
            );

            echo CJavaScript::jsonEncode(array(
                'success' => $success,
                'pathway_name' => $pathway->name,
                'points' => Game::getPoints(),
                'turn' => Game::getTurn(),
                'resources' => Resource::getAmounts(),
                'game_over' => Game::isGameOver(),
            ));
        }
    }

    public function actionEat()
    {
        if (isset($_POST['nutrients'])) {
            $success = Pathway::eat($_POST['nutrients']);

            echo CJavaScript::jsonEncode(array(
                'success' => $success,
                'pathway_name' => Pathway::EAT_NAME,
                'points' => Game::getPoints(),
                'turn' => Game::getTurn(),
                'resources' => Resource::getAmounts(),
                'game_over' => Game::isGameOver(),
            ));
        }
    }

    public function actionResourceVisual()
    {
        if (isset($_POST['resource'])) {
            $resource = Resource::model()->findByAttributes(array('id' => $_POST['resource']));
            echo CJavaScript::jsonEncode(array(
                'visual' => $this->renderPartial('resource-visual', array('resource' => $resource), true),
                'resource' => $resource->id,
                'resource_name' => $resource->name,
                'sources' => $resource->getSources(),
                'destinations' => $resource->getDestinations(),
            ));
        }
    }
}