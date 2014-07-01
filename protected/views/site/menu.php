<?php
/**
 * @param user
 */
?>

<div id="menu">
    <div class="tabs">
        <div class="tab active" for="new-game">
            <i class="fa fa-certificate"></i>
            <p class="title">New Game</p>
            <p class="subheading">start a new game</p>
        </div>

        <div class="tab" for="tutorial">
            <i class="fa fa-graduation-cap"></i>
            <p class="title">Tutorial</p>
            <p class="subheading">learn how to play</p>
        </div>

        <div class="tab" for="data">
            <i class="fa fa-database"></i>
            <p class="title">Data</p>
            <p class="subheading">load or save a game</p>
        </div>
        
        <div class="tab" for="account">
            <i class="fa fa-user"></i>
            <p class="title">Account</p>
            <?php if ($user === null): ?>
                <p class="subheading">log in</p>
            <?php else: ?>
                <p class="subheading">logged in as <?= $user->username ?></p>
            <?php endif ?>
        </div>

        <div class="tab" for="settings">
            <i class="fa fa-wrench"></i>
            <p class="title">Settings</p>
            <p class="subheading">adjust settings</p>
        </div>

        <div class="tab" for="about">
            <i class="fa fa-building"></i>
            <p class="title">About Us</p>
            <p class="subheading">team info and contact</p>
        </div>

        <div class="space"></div>
    </div>

    <div class="contents">
        <div class="content new-game active"></div>

        <div class="content tutorial"></div>

        <div class="content data"></div>

        <div class="content account">
            
            <?php if ($user === null): ?>
                <h2>Log In</h2>

                <div class="form">
                    <form id="login">
                        <input type="text" class="username" placeholder="Username" verify="no" info="">

                        <div class="add-on-holder">
                            <input class="password" type="password" placeholder="Password" verify="no" info="">
                            <div class="forgot-password add-on right">
                                <i class="fa fa-question-circle"></i>
                                <input class="btn mini" type="button" value="Forgot Password">
                            </div>
                        </div>

                        <input class="submit btn small" type="submit" value="Submit">
                    </form>

                    <div class="form-info"></div>
                </div>

                <h2>Create Account</h2>

                <div class="form">
                    <form id="create-account">
                        <input type="text" class="username" placeholder="Username" info="Your username identifies you and is used to log in.<br>Requirements:<ul><li>3-16 alphanumeric characters, including - and _</li><li>not taken by another player</li></ul>">
                        <input type="text" class="email" placeholder="Email Address" info="Your email address can be used to recover your account if you forget your password.<br>Requirements:<ul><li>valid email address, i.e. me@example.com</li><li>not used by another player</li></ul>">
                        <input type="password" class="new-password" placeholder="Password" info="A password is used to verify yourself when logging in. Use a strong password and keep it secret.<br>Requirements:<ul><li>3-32 alphanumeric or punctuation characters</li></ul>">
                        <input type="password" class="confirm" placeholder="Confirm Password" info="Repeat the password given above to make sure it's correct.">
                        <input type="submit" class="submit btn small" value="Submit">
                    </form>

                    <div class="form-info"></div>
                </div>

                <p class="footer">Logging in or creating an account will restart your game.</p>
                
            <?php else: ?>
                <h2>Change Password</h2>

                <div class="form">
                    <form id="change-password">
                        <input type="password" class="password" placeholder="Current Password" verify="no" info="Enter your current password for authentication.">
                        <input type="password" class="new-password" placeholder="New Password" info="Enter your new password.<br>Requirements:<ul><li>3-32 alphanumeric or punctuation characters</li></ul>">
                        <input type="password" class="confirm" placeholder="Confirm New Password" info="Repeat the new password above to make sure it's correct.">
                        <input type="submit" class="submit btn small" value="Submit">
                    </form>

                    <div class="form-info"></div>
                </div>

                <h2>Edit Email</h2>

                <div class="form">
                    <form id="email-info" class="add-on-holder">
                        <div class="add-on-holder">
                            <input class="email" type="text" placeholder="Email" value="<?= $user->email ?>" disabled>

                            <?php if ($user->email_verification->verified): ?>
                                <div class="verified add-on right" verified>
                                    <i class="fa fa-check"></i>
                                    <p>Email Verified</p>
                                </div>
                            <?php else: ?>
                                <div class="verified add-on right">
                                    <i class="fa fa-exclamation"></i>
                                    <button class="resend-email btn mini">Resend Verification Email</button>
                                </div>
                            <?php endif ?>

                            <div class="edit-email add-on right">
                                <i class="fa fa-edit"> </i>
                                <input type="button" class="btn mini" value="Edit">
                            </div>
                        </div>

                        <div id="edit-email">
                            <input class="password" type="password" placeholder="Current Password" verify="no" info="Enter your current password for authentication.">
                            <input class="email" type="text" placeholder="New Email" info="Enter your new email address.<br>Requirements:<ul><li>valid email address, i.e. me@example.com</li><li>not used by another player</li></ul>">
                            <input class="submit btn small" type="button" value="Submit">
                        </div>
                    </form>

                    <div class="form-info"></div>
                </div>

                <input type="button" class="btn" id="logout" value="Log Out">
            <?php endif ?>

        </div>

        <div class="content settings">
            <h2>Color Themes</h2>

            <div class="themes">
                <?php foreach (glob("{css/themes/light/*.css,css/themes/dark/*.css}", GLOB_BRACE) as $css):
                    $theme = basename($css, '.css');
                    $type = basename(dirname($css)); ?>

                    <div class="theme" theme="<?= $theme ?>" type="<?= $type ?>">
                        <p class="name"><?= ucfirst($theme) ?> [<?= ucfirst($type) ?>]</p>

                        <div class="add-on-holder">
                            <input type="text" placeholder="Text Field">
                            <div class="verified add-on right" verified>
                                <i class="fa fa-check"></i>
                            </div>
                        </div>

                        <button class="btn select">Select</button>
                    </div>
                <?php endforeach ?>
            </div>
        </div>

        <div class="content about">
            <h2>About <?= Yii::app()->name ?></h3>
            <p><?= Yii::app()->name ?> is an educational game created to teach students about cellular metabolism. Most appropriate for college-level purposes, the game is designed to be integrated into a classroom and provide players with an enjoyable and wholistic view of the metabolic process.</p>

            <h2>The Team</h3>
            <p><?= Yii::app()->name ?> was envisioned by Professor Neocles Leontis of Bowling Green State University for use in his biochemistry class. Having created a simple card game that allowed students to view metabolism as a whole, he partnered with Dominic Zirbel to develop an online version to allow for interactability and useability.</p>

            <h2>Contact Us</h3>
            <p>We would appreciate any feedback, comments, or questions. Email us at <a href="mailto:<?= Yii::app()->params['email'] ?>"><?= Yii::app()->params['email'] ?></a>.</p>

            <p class="copyright">Copyright 2014 Neocles B. Leontis</p>
        </div>
    </div>
</div>