<!DOCTYPE>
<html>
    <head>
        <title><?php echo $cfg['site']['title']; ?></title>
        <meta http-equiv="Content-Type" content="Type=text/html; charset=utf-8" />
        <link href="<?= WEB_ROOT ?>public/css/style.css" rel="stylesheet" type="text/css"/>
        <script>
            var WEB_ROOT = '<?= WEB_ROOT ?>';
        </script>
        <script src="<?= WEB_ROOT ?>public/scripts/zen.js"></script>
        <script src="<?= WEB_ROOT ?>public/scripts/main.js"></script>
        <?php $this->renderSection('head'); ?>
    </head>
    <body>
        <!-- Header -->
        <div id="header">
            <div class="content">
                <div class="site-title">
                    <h1><?php echo $cfg['site']['name']; ?></h1>
                </div>
            </div>
        </div>	
        <!-- Main -->
        <div id="main">
            <?php $this->renderBody(); ?>
        </div>
        <!-- Footer -->
        <div id="footer">
            <div class="content">
                This is a test app for ZEN !
            </div>
            <div class="content">
                <div id='copyright'>
                    <p>
                        <span>
                            Copyright &#169; <?= date('Y'); ?>
                            <a href='<?php echo $cfg['site']['address']; ?>'><?php echo $cfg['site']['owner']; ?></a>
                        </span>
                    </p>
                </div>			
            </div>
        </div>

    </body>
</html>