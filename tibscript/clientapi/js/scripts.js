$(document).ready(function(){

    $('#research').on('click', function(){
        if ($('#uIdent').val() != '') {
            $('.loader').css('display','block');
            var temp2 = $('#uIdent').val();
            var temp = temp2.replace(/\D+/g,"");
            $.get('http://51.254.132.135/tibscript/clientapi/research.php?what='+temp, function(data){
                //console.log(data);
                //var obj = $.parseJSON(data);
                $('#tadaa').html($.parseJSON(data));
                //$('#uIdent').trigger('input');
                var temp2 = $('#uIdent').val();
                var temp = parseInt(temp2.replace(/\D+/g,""));
    /*
                if (temp) {
                    $('tbody').find('tr').each(function(i) {
                        //if ($(this).attr('cud').indexOf(temp) !== -1) {
                        if ($(this).attr('cud') == 'f'+temp) {
                            $(this).css('display','table');
                        } else {
                            $(this).css('display','none');
                        }
                    });
                } else {
                    $('tbody').find('tr').each(function(i) {
                        $('#uIdent').css('display','table');
                    });
                }
    */
                $('.loader').css('display','none');

                /*var i = 0;
                $('#tadaa').html('');
                while (i<obj.length) {
                    $('#tadaa').append(obj[i]);
                    i++;
                }*/
            });
        }
    });
    /*
    $('#uClientId').on('input', function(){
        $('#uIdent').val('');
        var temp = $(this).val();
        $('tbody').find('tr').each(function(i) {
            if ($(this).attr('cid').indexOf(temp) !== -1) {
                $(this).css('display','table');
            } else {
                $(this).css('display','none');
            }
        });
    });
    */


/*
    $('#uIdent').on('input', function(){
        //$('#uClientId').val('');
        var temp2 = $(this).val();
        var temp = parseInt(temp2.replace(/\D+/g,""));

        if (temp) {
            $('tbody').find('tr').each(function(i) {
                if ($(this).attr('cud').indexOf(temp) !== -1) {
                    $(this).css('display','table');
                } else {
                    $(this).css('display','none');
                }
            });
        } else {
            $('tbody').find('tr').each(function(i) {
                $(this).css('display','table');
            });
        }

    });
*/
    $(document).keyup(function(e){
        var code = e.which; // recommended to use e.which, it's normalized across browsers
        if(code==13)e.preventDefault();
        if(code==32||code==13||code==188||code==186){
            $('#research').click();
        } // missing closing if brace
    });

});