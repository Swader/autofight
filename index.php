<?php if (PHP_SAPI != 'cli') : ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Autofight - A PHP job interview task</title>
        <meta name="description" content="Autofight - a PHP job interview task in the form of a browser based text war game. Tutorial on how it was built available here: http://bit.ly/15oRHgm">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <style type="text/css">
            #main {
                max-width: 1024px;
                margin: auto;
            }
            .container {
                height: auto;
                overflow: hidden;
            }
            #content {
                float: none; /* not needed, just for clarification */
                /* the next props are meant to keep this block independent from the other floated one */
                width: auto;
                overflow: hidden;
                background-color: whitesmoke;
                padding: 10px;
                border-radius: 5px;
                border: 1px solid silver;
            }
            #right {
                width: 340px;
                float: right;
                margin-left: 20px;
            }
            footer {
                position: fixed;
                bottom: 0;
                left: 0;
                height: 25px;
                border-top: 1px solid black;
                background-color: silver;
                width: 100%;
                text-align: center;
                line-height: 25px;
                font-family: arial, sans-serif;
                font-size: 10pt;
            }
            footer a {color: black;}
        </style>

    </head>
    <body>

    <div id="main">
    <div style="height:110px; text-align: center">
        <script async src="http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Autofight - leader -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:728px;height:90px"
             data-ad-client="ca-pub-4479682942105698"
             data-ad-slot="2146011114"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    <div style="clear:both"></div>
    <div class="container">
    <div id="right">
        <script async src="http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Autofight - sidebox -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:300px;height:250px"
             data-ad-client="ca-pub-4479682942105698"
             data-ad-slot="3622744312"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
        <br/>
        <script async src="http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Autofight - sidebox -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:300px;height:250px"
             data-ad-client="ca-pub-4479682942105698"
             data-ad-slot="3622744312"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>

    <div id="content">

    <hr>
    <p>Full tutorial on how this was built and why available <a href="http://bit.ly/15oRHgm" title="Autofight - a PHP job interview task tutorial">here</a>.</p>
    <hr>

<?php endif; ?>

<?php
/** Autoloader helps us avoid requires and includes */
require_once 'autoload.php';
/** Utility methods help us with some common operations */
require_once 'utility_methods.php';
/** Use helps us avoid long class names */
use autofight\Army;
/** Check if we got the required params */
/** @var int $iArmy1 */
$iArmy1 = (PHP_SAPI == 'cli')
    ? (isset($argv[1]) ? $argv[1] : 0)
    : ((isset($_GET['army1'])) ? (int)$_GET['army1'] : 0);
/** @var int $iArmy2 */
$iArmy2 = (PHP_SAPI == 'cli')
    ? (isset($argv[2]) ? $argv[2] : 0)
    : ((isset($_GET['army2'])) ? (int)$_GET['army2'] : 0);
if (!$iArmy1 || !$iArmy2) {
    $sMsg = 'Two parameters are expected - army1 and army2.
    Cannot continue without both. ';
    switch (PHP_SAPI) {
        case 'cli':
            $sMsg .= 'Maybe try this (each number represents the';
            $sMsg .= ' size of one army): index.php 50 50' . PHP_EOL;
            break;
        default:
            $sMsg .= '<br />Maybe try this link:
            <a href="/?army1=50&army2=50" >Army 1 = Army 2 = 50</a>';
            break;
    }
    echo $sMsg;
} else {
    /**
     * Register available unit types
     */
    Army::addUnitType(new \autofight\Infantry());
    Army::addUnitType(new \autofight\Tank());
	Army::addUnitType(new \autofight\Priest());
    /**
     * Build armies
     */
    $oArmy1 = new Army($iArmy1);
    $oArmy2 = new Army($iArmy2);
    $oWar = new \autofight\War();
    /**
     * Register appropriate logger, depending on context
     */
    $oWar->setLogger(
        PHP_SAPI == 'cli'
            ? new \autofight\Loggers\LoggerCli()
            : new \autofight\Loggers\LoggerWeb()
    );
    /**
     * Start the war
     */
    //$oWar->addArmy($oArmy1->setLabel('Blue'))->addArmy($oArmy2->setLabel('Red'));
    $oWar->addArmy($oArmy1)->addArmy($oArmy2);
    $oWar->fight();
}
?>

<br />
<br />

<footer>
    By <a href="https://plus.google.com/104771776404197897488?rel=author">Bruno Škvorc</a>
</footer>

<?php if (PHP_SAPI != 'cli') : ?>

    </div>
    </div>
    </div>
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
        ga('create', 'UA-43657205-1', 'bitfalls.com');
        ga('send', 'pageview');
    </script>
    </body>
    </html>

<?php endif; ?>