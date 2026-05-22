import './bootstrap';
$('.post').find('.intercation').find('a').eq(2).on('click',function(){
    $('#edit-modal').modal();
});