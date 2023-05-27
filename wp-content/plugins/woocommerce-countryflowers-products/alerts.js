jQuery(document).ready(function($) {

    $('#dhg_inventory tfoot th').each( function (i) {
            $(this).html( '<select class="info_col form-control form-control-solid"><option value=""></option></select>' );
    } );

    var table = $('#dhg_inventory').DataTable( {
        initComplete: function () {
            this.api().columns().every( function () {
                
                var column = this;
                console.log(column);
                var select = $('<select><option value=""></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        },
        dom: 'Bfrtip',
        pageLength: 25,
        buttons: [
            'colvis', 'copy', 'excel', 'pdf'
        ]
      } );
 



});