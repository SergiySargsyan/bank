
//javascript validation
function isValid() {
    if($operation_text.val()==''){
        return 'Write description';
    }else if($operation_valueUAH.val()=='' || $operation_valueUAH == 0){
        return 'Write write money';
    }else if($operation_valueUSD.val()=='' || $operation_valueUSD == 0){
        return 'Can\'t contact with PrivatBank';
    }else{
        return true;
    }

}

$form = $('#form_add');
$operation_id = $("#operation_id");
$operation_text = $("#operation_text");
$operation_valueUAH = $("#operation_valueUAH");
$operation_valueUSD = $("#operation_valueUSD");
$operation_date = $("#operation_date");
$message = $('#error_message');
$add = $('#add');
$info = $('#info');
$(document).ready(function () {
    $course = null;
    $(this).load('https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5',function (res) {
        var array_obj = jQuery.parseJSON(res);
        array_obj.forEach(function (obj){
            if(obj.ccy == 'USD'){
                $course = obj.buy;
            }
        });
    });

    $add.click(function () {
        $(this).fadeOut(1000);
        $info.fadeOut();
        $form.fadeIn(2000);
    });

    $(".glyphicon-edit").click(function () {
        $info.fadeOut();
        $form.fadeIn();
        $operation_valueUSD.prop('disabled', false);

        var arr = [];
        $text = $(this).parent().siblings()[0];
        $valueUAH = ($(this).parent().siblings())[1];
        $valueUSD = ($(this).parent().siblings())[2];
        $date = (($(this).parent().siblings())[3]).innerHTML;
        // :-)
        arr.push($date.substr(0,4));
        arr.push($date.substr(5,2));
        arr.push($date.substr(8,2));
        arr.push($date.substr(11,2));
        arr.push($date.substr(14,2));
        $operation_date.find('select').each(function (i, elem) {
            var value = parseInt(arr[i]);
            $(this).val(value);
        });
        $operation_id.val($(this).attr("data-operation-id"));
        $operation_text.val($text.innerHTML);
        $operation_valueUAH.val($valueUAH.innerHTML);
        $operation_valueUSD.val($valueUSD.innerHTML);
        console.log($text);
        $($text).html($operation_text.val);
    });

    $(".glyphicon-trash").click(function () {
        $deleteTr = $(this).parent().parent();
        var id = $(this).attr("data-operation-id");
        deleteUrl = "/delete-" + id;
        $.ajax({
            type: 'GET',
            url: deleteUrl,
            success: (function (response) {
                // console.log($deleteTr);
                $deleteTr.fadeOut();
            }),
            error: (function (response){
                console.log(response);
            })

        });
    });

    $operation_valueUAH.keyup(function () {
        if ($course){
            var dollar_equivalent = ($(this).val())/$course;
            $operation_valueUSD.val(dollar_equivalent.toFixed(2));
        }else{
            $operation_valueUSD.val('Error load course');
        }
    });

    $form.on('submit', function (event) {
        event.preventDefault();
        var formIsValid = isValid();
        $message.empty();
        if (formIsValid!==true){
            $message.html(formIsValid + "<span class='glyphicon glyphicon-remove'></span>");
            $message.fadeIn();
            $message.click(function () {
                $(this).fadeOut();
            });
            return false;
        }
        $message.fadeOut();
        console.log("sub");

        var postParams = $form.serialize();

        $.ajax({
            type: 'POST',
            url: '/create',
            data: postParams,
            success: (function (response) {
                $info.attr('class','alert-success');
                $info.html(response);
                $info.fadeIn();
                $form.fadeOut();
                $add.fadeIn();
                if (response == 'Operation edit'){
                    $($text).html($operation_text.val);
                    $($valueUAH).html($operation_valueUAH.val);
                    $($valueUSD).html($operation_valueUSD.val);
                }
                $operation_text.val(null);
                $operation_valueUAH.val(null);
                $operation_valueUSD.val(null);
//
            }),
            error: (function (response){
                $info.attr('class','alert-danger');
                $info.html(response.responseText);
                $form.fadeOut();
                $add.fadeIn();
            })
        });
    })
})
