$(document).ready(
    function(){
       var iframe=$('iframe');

iframe.load(function(){
    alert("123");
    var form= iframe.contents().find('form');
    console.log(form);
    form .submit(function() {
        alert("123");
        var total=$('p span').text();
        var id=$('[name="id"]').val();
        var phone=$('[name="phone"]').val();
        $.get("index.php", {action: "paid",total:total,id:id,phone:phone});
    });
});




    }
);
