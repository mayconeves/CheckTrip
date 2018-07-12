<?php

header('Content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Headers: Content-Type');
header("Access-Control-Allow-Origin: *");

/**
 * Checar se o parametro existe
 */
if ( isset($_POST["url"]) ){

    $url = $_POST["url"];
    $status = '';

    /**
     * Encontrar a primeira ocorrencia da string "restaurante"
     * Se for true continua com a execucao
     */
    if( strstr($url, "Restaurant", true) ) {

        /**
         * Socilitar os dados da url
         */
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $html = curl_exec($ch);

        /**
         * Checar Http response
         */
        $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if( $info !== 200 ) {

            $status     = "Erro ao retornar os dados da url";
            $response   = [ 'status' => $status];
            $json       = json_encode($response, JSON_UNESCAPED_SLASHES);

            echo $json;
            exit();

        }

        /**
         * Se houver algum erro ao solicitar pagina
         */
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);

        /**
         * Dados a serem encontrados
         */
        $dom = new DOMDocument();

        @$dom->loadHTML($html);

        $classHeadingTitle      = "heading_title";
        $classPhone             = "phone";
        $classRating            = "rating";
        $classImg               = "onDemandImg";

        $finder                 = new DomXPath($dom);
        $dataHeadingTitle       = $finder->query("//*[contains(@class, '$classHeadingTitle')]");
        $dataPhone              = $finder->query("//*[contains(@class, '$classPhone')]");
        $dataRating             = $finder->query("//*[contains(@class, '$classRating')]");
        $dataImgs                = $finder->query("//*[contains(@class, '$classImg')]");


        $name                   = cleanSpace( strip_tags( $dom->saveHtml($dataHeadingTitle[0])) );
        $phone                  = cleanSpace( strip_tags( $dom->saveHtml($dataPhone[0])) );
        $reviews                = cleanReviews( $dom->saveHtml($dataRating[0]) );
        $status                 = "Sucesso";


        $images = [];

        foreach ($dataImgs as $dataImg) {
            $images[] = cleanSpace( $dataImg->getAttribute('data-src') );
        }

        $response = ['name' => $name, 'phone' => $phone, 'reviews' => $reviews, 'images' => $images, 'status' => $status];

        //print_r($response);

        $json = json_encode($response, JSON_UNESCAPED_SLASHES);

        echo $json;
        exit();

    }else{

        $status     = "A pesquisa solicitada retornou um critério inválido, verifique os dados e tente novamente";
        $response   = [ 'status' => $status];
        $json       = json_encode($response, JSON_UNESCAPED_SLASHES);

        echo $json;
        exit();
    }
}else{

    $status     = "Url vazia, verifique os dados e tente novamente";
    $response   = [ 'status' => $status];
    $json       = json_encode($response, JSON_UNESCAPED_SLASHES);

    echo $json;
    exit();
}

/**
 * Remover espacos em branco
 * Em todos os lugares por espacos em branco validos
 * Remove o html
 */
function cleanSpace( $var ){
    $var = trim(preg_replace('/\s+/', ' ', $var ));
    return $var;
}

/**
 * Remover valores desnecessarios ao numero de reviews
 */
function cleanReviews( $var ){
    $var = strtok(preg_replace('/\s+/', ' ', strip_tags( $var )), " ");
    return $var;
}

