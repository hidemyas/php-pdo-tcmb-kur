<?php
try{
    $db_connect =   new PDO('mysql:dbname=egtim;host=localhost','root','');
}catch (PDOException $exception){
    echo "MYSQL Bağlantı Hatası <br/>";
    echo "Hata Açıklaması : ".$exception->getMessage();
    die();
}
?>
<!doctype html>
<html lang="tr-TR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TCMB Döviz Kurları</title>
</head>
<body>
<?php
$url = "https://www.tcmb.gov.tr/kurlar/today.xml";
$content = simplexml_load_file($url);

//echo "<pre>";
//print_r($content);
//echo "</pre>";

//$USD_count = $content->Currency[0]->Unit;
//$USD_name = $content->Currency[0]->Isim;
//$USD_forex_buy = $content->Currency[0]->ForexBuying;
//$USD_forex_sell = $content->Currency[0]->ForexSelling;
//$USD_bank_buy = $content->Currency[0]->BanknoteBuying;
//$USD_bank_sell = $content->Currency[0]->BanknoteSelling;

function saveDB($code,$name,$unit,$ForexBuying=0,$ForexSelling=0,$BanknoteBuying=0,$BanknoteSelling=0)
{
    global $db_connect;
    $check_code =   $db_connect->prepare('SELECT * FROM dovizkurlari WHERE kodu=?');
    $check_code->execute([$code]);
    $check_code_count   =   $check_code->rowCount();
    $time=time();
    if ($check_code_count>0){
        $update_code    =   $db_connect->prepare('UPDATE dovizkurlari SET birim=?,alis=?,satis=?,efektifalis=?,efektifsatis=?,guncellenmezamani=? WHERE kodu=?');
        $update_code->execute([$unit,$ForexBuying,$ForexSelling,$BanknoteBuying,$BanknoteSelling,$time,$code]);
    }else{
        $add_code   =   $db_connect->prepare('INSERT INTO dovizkurlari (adi,kodu,birim,alis,satis,efektifalis,efektifsatis,guncellenmezamani) values (?,?,?,?,?,?,?,?)');
        $add_code->execute([$name,$code,$unit,$ForexBuying,$ForexSelling,$BanknoteBuying,$BanknoteSelling,$time]);
//        $add_code_count =   $add_code->rowCount();
    }
}

?>

<table width="750" border="0" align="center" cellpadding="0" cellspacing="0">
    <thead>
    <tr height="30" bgcolor="#ccc">
        <th width="2550">Adı</th>
        <th width="2550">Kod</th>
        <th width="100">Brimi</th>
        <th width="100">Alış</th>
        <th width="100">Satış</th>
        <th width="125">Efektif Alış</th>
        <th width="125">Efektif Satış</th>
    </tr>
    </thead>
    <tbody >
    <?php $num=0; while ($num<21):
        $name   = $content->Currency[$num]->Isim;
        $code   = $content->Currency[$num]['CurrencyCode'];
        $unit   = $content->Currency[$num]->Unit;
        $ForexBuying   = $content->Currency[$num]->ForexBuying;
        $ForexSelling   = $content->Currency[$num]->ForexSelling;
        $BanknoteBuying   = $content->Currency[$num]->BanknoteBuying;
        $BanknoteSelling   = $content->Currency[$num]->BanknoteSelling;
        ?>
    <tr height="30">
        <th width="2550"><?php echo $name; ?></th>
        <th width="2550"><?php echo $code; ?></th>
        <th width="100"><?php echo $unit; ?></th>
        <th width="100"><?php echo $ForexBuying; ?></th>
        <th width="100"><?php echo $ForexSelling; ?></th>
        <th width="125"><?php echo $BanknoteBuying; ?></th>
        <th width="125"><?php echo $BanknoteSelling; ?></th>
    </tr>
    <?php
        saveDB($code,$name,$unit,$ForexBuying,$ForexSelling,$BanknoteBuying,$BanknoteSelling);
        $num++; endwhile;?>
    </tbody>

</table>

<?PHP
/*
SimpleXMLElement Object
(
[@attributes] => Array
    (
        [Tarih] => 07.02.2025
        [Date] => 02/07/2025
        [Bulten_No] => 2025/27
    )

[Currency] => Array
    (
        [0] => SimpleXMLElement Object
            (
                [@attributes] => Array
                    (
                        [CrossOrder] => 0
                        [Kod] => USD
                        [CurrencyCode] => USD
                    )

                [Unit] => 1
                [Isim] => ABD DOLARI
                [CurrencyName] => US DOLLAR
                [ForexBuying] => 35.9055
                [ForexSelling] => 35.9702
                [BanknoteBuying] => 35.8804
                [BanknoteSelling] => 36.0242
                [CrossRateUSD] => SimpleXMLElement Object
                    (
                    )

                [CrossRateOther] => SimpleXMLElement Object
                    (
                    )

            )


*/
?>
</body>
</html>
<?php $db_connect=null; ?>