<?php include 'init.php'; ?>
<?= styleLoadHelper::StyleLoad('mainStyle'); ?>
<?= jsLoadHelper::loadExternalJs('https://kit.fontawesome.com/0fe0f35e7d.js') ?>
<?php include "helpers/menuCounterHelper.php"; ?>


<body>

<div id="app-name-header">
   <img src="media/img/app_logo.png" style="height: 50px;">
    <h3 style="color:blue;">Panel moderacji zleceń kurierskich Express IT</h3>
    wersja 0.3 alpha (<b><a href="media/changelog.html">zobacz changelog</a></b>)
</div>


<div id="app-menu">
    <?php $count = menuCounterHelper::countOrders(); ?>

    <a href="?task=archiveController&action=createCustomOrder" class="w3-btn w3-white w3-border w3-border-blue w3-round"><i class="fas fa-plus-square" style="
    color: #00bcd4;
    font-size: 15px;
"></i> Stwórz przesyłkę</a>

    <a href="?task=indexController&action=getNewOrders" class="w3-btn w3-white w3-border w3-border-blue w3-round">Pobierz nowe <span id="menu-counter"><?= $count['new']; ?></span></a>
    <a href="?task=qeueController&action=index" class="w3-btn w3-white w3-border w3-border-blue w3-round">Oczekujące <span id="menu-counter"><?= $count['waiting']; ?></span></a>
    <a href="?task=completedController&action=index" class="w3-btn w3-white w3-border w3-border-blue w3-round">Zamówione <span id="menu-counter"><?= $count['label_sent_to_client']; ?></span></a>
    <a href="?task=archiveController&action=index" class="w3-btn w3-white w3-border w3-border-blue w3-round">Archiwum <span id="menu-counter"><?= $count['archived']; ?></span></a>
    <a href="?task=sendBackController&action=index" class="w3-btn w3-white w3-border w3-border-blue w3-round">Odesłane <span id="menu-counter"><?= $count['return_shipment_created']; ?></span></a>
    <a href="?task=trashController&action=index" class="w3-btn w3-white w3-border w3-border-blue w3-round">Kosz <span id="menu-counter"><?= $count['trashed']; ?></span></a>

    <div id="finder" style="float: right;">

    <form method="POST" enctype="multipart/form-data" action="?task=finderController&action=find">
        <input type="text" id="finder_phrase" name="finder_phrase" placeholder="Wpisz szukaną frazę" style="width: 335px; height: 35px;"/>
        <input name="finder_submit" type="submit" value="Szukaj" style="width: 90px; height: 35px; background: #cddc39; border: 1px; border-style: solid; border-radius: 4px; border-color: white;">
        <br/>

        <div id="search_by_radio" style="color: #ffffff;padding: 1px;">
            Szukaj po:

            <input type="radio" id="client_surname" name="search_by" value="client_surname" checked>
            <label for="search_by">Nazwisko/Firma</label>

            <input type="radio" id="client_phone" name="search_by" value="client_phone">
            <label for="search_by">Telefon</label>

            <input type="radio" id="laptop_model" name="search_by" value="laptop_model">
            <label for="search_by">Model laptopa</label>
        </div>


    </form>
    </div>

</div>

<div id="bg-image"></div>
<div id="main-content">
    <?php

        if(isset($_GET['task'], $_GET['action']) && !empty($_GET['task']) && !empty($_GET['action']))
        {
            $controller = $_GET['task'];
            $method = $_GET['action'];

            error_reporting(E_ALL);
            ini_set("display_errors", 1);

            $file = 'controllers/' . $_GET['task'] . '.php';
            include $file;

            $object = new $controller;
            $object -> $method();
        }

    ?>
</div>

<?= footerHelper::getFooter(); ?>
<?= jsLoadHelper::loadJs('mainJs'); ?>
</body>


