<script>
    function formatRupiahNumbers() {
        $('.rupiahNumber').each(function () {
            let value = $(this).val().replace(/[^\d-]/g, '');
            value = parseInt(value, 10);
            if (!isNaN(value)) {
                $(this).val(value.toLocaleString('de-DE'));
            } else {
                $(this).val('');
            }
        });
    }

    // function funcFormatRupiahNumbers(val) {
    //     let value = val.replace(/[^\d]/g, '');
    //     value = parseInt(value, 10);
    //     if (!isNaN(value)) {
    //         return value.toLocaleString('de-DE');
    //     } else {
    //         return 0;
    //     }
    // }

    function funcFormatRupiahNumbers(val) {
        // Remove all characters except digits and a leading minus sign
        let value = val.replace(/[^\d-]/g, '');

        // Convert the cleaned string to an integer
        value = parseInt(value, 10);

        // Check if the value is a valid number
        if (!isNaN(value)) {
            // Format the number as a string in the "de-DE" locale
            return value.toLocaleString('de-DE');
        } else {
            // Return 0 if the input is not a valid number
            return 0;
        }
    }

    function formatNumberVal(num) {
        return num.toString().replace(/\./g, '');
    }

    // Apply formatting on document ready
    formatRupiahNumbers();

    // Reapply formatting on input
    $(document).on('input', '.rupiahNumber', function () {
        formatRupiahNumbers();
    });

    $(document).ready( function () {
        $('#set-data').DataTable({
            stateSave: true
        });
        $('body').removeClass('sidebar-hidden')
        $(".alert").fadeOut(5000);
        // Default Datatables
        $('.smt-table').DataTable();
        // bind change event to select
        $('#smt_navigation').on('change', function () {
            var url = $(this).val(); // get selected value
            if (url) { // require a URL
                window.location = url; // redirect
            }
            return false;
        });
    } );
</script>

