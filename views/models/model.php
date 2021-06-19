<?php

abstract class model
{
    protected $dbExpressitFormOrders;
    protected $dbDhlOrders;

    public function __construct()
    {
        $alphaDbServer = 1; // 0 = local, 1 = remote
        if($alphaDbServer == 0)
        {
            $expressItRemote = 'localhost';
            $alphaDbName = 'admin_alpha';
            $alphaDbUser = 'root';
            $alphaDbPass = '';
        }
        if($alphaDbServer == 1)
        {
            $expressItRemote = '188.40.235.79';
            $alphaDbName = 'admin_alpha';
            $alphaDbUser = 'admin_alpha';
            $alphaDbPass = 'Letmeowned1337';
        }

        try
        {
            $this -> dbExpressitFormOrders = new PDO('mysql:host=' . $expressItRemote . ';dbname=' . $alphaDbName . ';charset=utf8;port=3306', $alphaDbUser, $alphaDbPass);

        }
        catch(PDOException $e)
        {
            exit('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>Błąd połączenia z bazą danych ' . $e -> getMessage() . '!');
        }

        try
        {
            $this -> dbDhlOrders = new PDO('mysql:host=' . 'localhost' . ';dbname=' . 'dhl24orders' . ';charset=utf8;port=3306', 'root', '');

        }
        catch(PDOException $e)
        {
            exit('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>Błąd połączenia z bazą danych ' . $e -> getMessage() . '!');
        }

    }
}
