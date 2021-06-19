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
            $expressItRemote = '...';
            $alphaDbName = '...';
            $alphaDbUser = '...';
            $alphaDbPass = '';
        }
        if($alphaDbServer == 1)
        {
            $expressItRemote = '...';
            $alphaDbName = '...';
            $alphaDbUser = '...';
            $alphaDbPass = '...';
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
            $this -> dbDhlOrders = new PDO('mysql:host=' . 'localhost' . ';dbname=' . 'admin_dhl24' . ';charset=utf8;port=3306', 'admin_dhl24', 'v6xJvs1yDd');
        }
        catch(PDOException $e)
        {
            exit('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>Błąd połączenia z bazą danych ' . $e -> getMessage() . '!');
        }
    }
}
