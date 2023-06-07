 <!-- поиск  -->
$(document).ready(function() {
    $('#filter-btn').click(function() {
        var searchVal = $('#search').val().toLowerCase();
        if (searchVal === '') {
            $('.table tbody tr').show();
            $('#search-no-results').hide();
        } else {
            $('.table tbody tr').each(function(index, row) {
                var allCells = $(row).find('td');
                if(allCells.length > 0) {
                    var found = false;
                    allCells.each(function(index, td) {
                        var regExp = new RegExp(searchVal, 'i');
                        if(regExp.test($(td).text())) {
                            found = true;
                            return false;
                        }
                    });
                    if(found === true) {
                        $(row).show();
                    } else {
                        $(row).hide();
                    }
                }
            });
            if($('.table tbody tr:visible').length === 0) {
                $('#search-no-results').text('Такой задачи нет').show();
            } else {
                $('#search-no-results').hide();
            }
        }
    });
});
