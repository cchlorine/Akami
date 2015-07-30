<?php

require 'Akami/Akami.php';
\Akami\Akami::registerAutoloader();

$app = new \Akami\Akami();
$app->get('/', function () {
    $template = <<<EOT
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Akami Framework</title>
</head>
<body>
    <style>
        html, body {
            height: 100%;
            padding: 0;
            margin: 0;
        }

        body {
            width: 100%;
            display: table;
            text-align: center;
            background: #16938A;
            color: #333;
            font-size: 16px;
            line-height: 1.825;
            font-family: "Segoe UI", "Lucida Grande", Helvetica, Arial, "Microsoft YaHei", FreeSans, Arimo, "Droid Sans","wenquanyi micro hei","Hiragino Sans GB", "Hiragino Sans GB W3", Arial, sans-serif
        }

        .box {
            display: table-cell;
            vertical-align: middle;
        }

        .box .container {
            background: #fff;
            width: 500px;
            margin: 0 auto;
            padding: 1.5em 2em 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .2);
            -webkit-box-sizing: border-box;
                    box-sizing: border-box;
        }

        #header {
            color: #fff;
            padding: 0 0 1em;
            background: #16938A;
        }

        #header h1 {
            margin: 0;
            font-size: 50px;
            font-weight: 200;
        }

        #header p {
            margin: -.5em 0 0;
        }

        h2 {
            font-weight: 200;
            margin: 1em 0 -.5em;
        }

        .container > p {
            color: #999;
        }

        #footer {
            color: #fff;
            font-size: 14px;
            position: relative;
            bottom: -3em;
            margin-top: -2em;
        }
    </style>
    <div class="box">
        <div class="container">
            <header id="header">
                <h1>Akami</h1>
                <p>A tiny PHP Framework.</p>
            </header>
            <h2>Welcome to Akami!</h2>
            <p>Congratulations! Your Akami application is running.</p>
            <a href="https://github.com/Kunr/Akami">
                <svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 50 50"><path fill-rule="evenodd" clip-rule="evenodd" fill="#181616" d="M25 10c-8.3 0-15 6.7-15 15 0 6.6 4.3 12.2 10.3 14.2.8.1 1-.3 1-.7v-2.6c-4.2.9-5.1-2-5.1-2-.7-1.7-1.7-2.2-1.7-2.2-1.4-.9.1-.9.1-.9 1.5.1 2.3 1.5 2.3 1.5 1.3 2.3 3.5 1.6 4.4 1.2.1-1 .5-1.6 1-2-3.3-.4-6.8-1.7-6.8-7.4 0-1.6.6-3 1.5-4-.2-.4-.7-1.9.1-4 0 0 1.3-.4 4.1 1.5 1.2-.3 2.5-.5 3.8-.5 1.3 0 2.6.2 3.8.5 2.9-1.9 4.1-1.5 4.1-1.5.8 2.1.3 3.6.1 4 1 1 1.5 2.4 1.5 4 0 5.8-3.5 7-6.8 7.4.5.5 1 1.4 1 2.8v4.1c0 .4.3.9 1 .7 6-2 10.2-7.6 10.2-14.2C40 16.7 33.3 10 25 10z"/></svg>
            </a>
            <footer id="footer">
                <p>&copy; 2015 Rakume Hayashi</p>
            </footer>
        </div>
    </div>
</body>
</html>
EOT;
  echo $template;
});

$app->run();