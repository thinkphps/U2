jQuery(function($){
  $('.official').on('click', '.js-official-alter', function(){
    var alter = $(this),
        p = alter.siblings('p.js-official-id'),
        save = alter.next(),
        input = alter.parent().find('input')

    input.val(p.text()).removeClass('hide').focus()
    p.addClass('hide')
    save.removeClass('hide')
    alter.addClass('hide')

  }).on('click', '.js-official-save', function(){
    var save = $(this),
        p = save.siblings('p.js-official-id'),
        input = save.parent().find('input'),
        alter = save.prev()

    p.text(input.val()).removeClass('hide')
    alter.removeClass('hide')
    save.addClass('hide')
    input.addClass('hide')

  }).on('keypress', 'input', function(e){
    if(e.which == 13){
        $(this).parent().siblings('.js-official-save').click()
    }
  }).on('click', 'p.js-official-id', function(){
    $(this).siblings('.js-official-alter').click()
  })


  $('form.products-edit').on('click', 'input:checkbox', function(){
    var that = $(this),
        parent = that.parents('.form-group'),
        checked = that.prop('checked'),
        allselect = parent.find('input.allselect'),
        checkbox = parent.find('input:checkbox'),
        num = parent.data('num') || 0,
        len = checkbox.length - 1
    
    if(that.is(allselect)){
        num = checked ? len : 0
        checkbox.prop('checked', checked)
    } else {
        num += checked? 1 : -1
        allselect.prop('checked', num === len && checked)
    }
    parent.data('num', num)
  })
})