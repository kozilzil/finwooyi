(function($) {
  'use strict';
  $(function() {
    var todoListItem = $('.todo-registrants');
    var todoListInput = $('.todo-registrants-input');
    $('.todo-registrants-add-btn').on("click", function(event) {
      event.preventDefault();

      var item = $(this).prevAll('.todo-registrants-input').val();

      if (item) {
        todoListItem.append("<li><div class='form-check'><label class='form-check-label'><input class='checkbox' type='checkbox'/>" + item + "<i class='input-helper'></i></label></div><i class='remove ti-close'></i></li>");
        todoListInput.val("");
      }

    });

    todoListItem.on('change', '.checkbox', function() {
      if ($(this).attr('checked')) {
        $(this).removeAttr('checked');
      } else {
        $(this).attr('checked', 'checked');
      }

      $(this).closest("li").toggleClass('completed');

    });

    todoListItem.on('click', '.remove', function() {
      $(this).parent().remove();
    });

  });
})(jQuery);
