<?php
/**
 * @param primary_resources
 * @param non_global
 */
?>

<div id="footer">
    <div id="minimize-footer"></div>

    <div class="content">
        <div id="filter">
            <div class="row">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-search"></i></span>
                    <input class="name" type="text" placeholder="Filter By Name">
                </div>
            </div>
            
            <div class="row">
                <div class="buttons">
                    <div class="btn-group" data-toggle="buttons-checkbox">
                        <input class="available btn btn-mini" type="button" value="Available">
                        <input class="unavailable btn btn-mini" type="button" value="Unavailable">
                    </div>
                    <div class="btn-group" data-toggle="buttons-checkbox">
                        <input class="catabolic btn btn-mini" type="button" value="Catabolic">
                        <input class="anabolic btn btn-mini" type="button" value="Anabolic">
                    </div>
                </div>
            </div>

            <div class="row">
                <div>
                    <div class="input-prepend">
                        <span class="add-on"><i class="icon-search"></i></span>
                        <input class="reactant" type="text" placeholder="Reactant">
                    </div>

                    <div class="input-prepend">
                        <span class="add-on"><i class="icon-search"></i></span>
                        <input class="product" type="text" placeholder="Product">
                    </div>
                </div>
            </div>

            <div class="row">
                <div>
                    <label><input class="passive" type="checkbox" checked>Show Automatic Processes</label>
                    <input class="clear btn btn-small" type="button" value="Display All">
                </div>
            </div>
        </div>

        <table id="trackers">
            <?php foreach ($primary_resources as $resource): ?>
                <td class="tracker res-info-source" res="<?= $resource->id ?>">
                    <div class="header">
                        <p class="title"><?= $resource->name ?></p>
                        <p class="total"><?= $resource->getTotal() ?></p>
                    </div>

                    <?php foreach ($non_global as $organ): ?>
                        <div class="organ" organ="<?= $organ->id ?>">
                            <p class="amount"><?= $resource->getAmount($organ->id) ?></p>
                            <p class="name"><?= $organ->name ?></p>
                            <div class="icons"></div>
                        </div>
                    <?php endforeach ?>
                </td>
            <?php endforeach ?>

            <td class="tracker actions">
                <div class="header">
                    <p class="title">Organ-Specific Actions</p>
                </div>

                <?php foreach ($non_global as $organ): ?>
                    <div class="organ" organ="<?= $organ->id ?>">
                        <?php if ($organ->hasAction()): ?>
                            <p class="amount"><?= $organ->getActionCount() ?></p>
                            <p class="name"><?= $organ->action_name ?></p>
                        <?php endif ?>
                    </div>
                <?php endforeach ?>
            </td>
        </table>

        <div id="resource-visual">
            <div>
                <div class="header">
                    <p class="name">Name</p>
                    <i class="icon-remove"></i>
                </div>
                <div class="content">
                    <div>
                        <p class="aliases"></p>
                        <p class="formula"></p>
                        <p class="hard_min"></p>
                        <p class="hard_max"></p>
                        <p class="soft_min"></p>
                        <p class="soft_max"></p>
                        <p class="description"></p>
                    </div>
                    <div>
                        <img class="image" alt="" src="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
