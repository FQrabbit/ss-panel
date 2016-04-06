$(document).ready(function() {
    
    $('#userTable tfoot th').each( function () {
        var title = $('#userTable thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="'+title+'" />' );
    } );
    var table = $('#userTable').DataTable({
        "scrollY": "418px",
        "scrollX": false
        });

    table.columns().eq( 0 ).each( function ( colIdx ) {
        $( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () {
            table
                .column( colIdx )
                .search( this.value )
                .draw();
            } );
        } );
} );