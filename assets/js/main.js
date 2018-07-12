$(document).ready(function () {

    /**
     * Evento ao clicar em buscar
     */
    $('#btnBuscar').on('click', function (e) {
        e.preventDefault();

        $('.loader').removeClass('hide');
        $('#sessionOne').hide();


        /**
         * Recupera a url preenchida, removendo os espacos antes e depois
         * @type {string}
         */
        var form_url  = ($('.input-url').val()).trim();

        /**
         * Url que ira recuperar as informacoes da url
         */
        var data_url    = "./check-trip.php";

        /**
         * Verifica se a url informada e valida
         */
        if( form_url.startsWith("https://www.tripadvisor.com.br/Restaurant")) {
            /**
             * Faz a chamada ao ajax
             */
            $.post( data_url, {
                 url: form_url
            })
                .done(function ( resultado ) {
                    /**
                     * Verificar se o status for sucesso, com os dados
                     */
                if ( resultado.status == "Sucesso" ){

                    $('.loader').addClass('hide');
                    $('#sessionTwo').removeClass('hide');

                    /**
                     * Popular com os valores encontrados
                     */
                    $('#nome').html(resultado.name);
                    $('#telefone').html("Telefone " + resultado.phone);
                    $('#reviews').html(resultado.reviews + ' reviews');
                    $('#carousel').removeClass('hide');

                    /**
                     * Percorrer cada imagem populando o carousel
                     */
                    $.each( resultado.images, function ( key, value) {

                        $('#carousel-populate').append(
                            '<div class="carousel-item">\n' +
                            '<img src="' + value +'" alt="' + resultado.name + '">\n' +
                            '</div>'
                        );
                    } );
                    /**
                     * Encontrar o primeiro item do caroussel e adiciona a classe active
                     */
                    $('.carousel-item').first().addClass('active');


                }else{

                    $('.loader').addClass('hide');
                    $('#sessionTwo').removeClass('hide');
                    $('#status').html(resultado.status);
                }

            });
        }else{
            /**
             * Mensagem de erro na url informada
             */
            $('.loader').addClass('hide');
            $('#sessionTwo').removeClass('hide');
            $('#status').html('Url inválida');
        }
    });

    /**
     * Buscar novo restaurante
     */
    $('#btnNovaPesquisa').on('click', function (e) {
        e.preventDefault();

        clearMessage();

        $('#sessionOne').show();
        $('#sessionTwo').addClass('hide');
        $('#carousel').addClass('hide');
    });

    /**
     * Funcao para limpar os campos
     */
    function clearMessage() {
        $('.input-url').val('');
        $('#nome').html('');
        $('#telefone').html('');
        $('#reviews').html('');
        $('#status').html('');
    }

});
