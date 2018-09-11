<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

    <?php require_once 'elements/head.php'; ?>

    <body>
        <?php require_once 'elements/header.php'; ?>

    <style>
        .btn-sm{
            margin: 10px;
        }
    </style>
    <section id="services" class="section section-padded">


        <form action="<?php echo site_url("welcome/detailed_full_analysis") ?>" method="post" target="_blank" id="full_analysis">
            <a href="javascript:" onclick="removeDay()" class="btn-sm btn-danger pull-right"><span class="fa fa-minus"></span> Remove Day</a>
            <a href="javascript:" onclick="addNewDay()" class="btn-sm btn-primary pull-right"><span class="fa fa-plus"></span> Add Day</a>
            <a href="javascript:" onclick="document.getElementById('full_analysis').reset(); " class="btn-sm btn-danger pull-right"><span class="fa fa-close"></span> Clear Values</a>    
            <?php require_once 'elements/formFields.php'; ?>
            <button type="submit" class="btn btn-primary active" onclick="detailedFullAnalysisSubmit()">View The Full analysis</button>
            <button type="submit" class="btn btn-primary active" onclick="detailedFifteenthAnalysisSubmit()">View The 15 matches</button>
        </form>
    </section>
    <script>
        
        function detailedFullAnalysisSubmit(){
            actionUrl = '<?php echo site_url("welcome/detailed_full_analysis");?>';
            $('#full_analysis').attr('action', actionUrl).submit();
        }
        
        function detailedFifteenthAnalysisSubmit(){
            actionUrl = '<?php echo site_url("welcome/detailed_fifteenth_analysis_closePrice");?>';
            $('#full_analysis').attr('action', actionUrl).submit();
        }
        
        function addNewDay() {
            var row = $('#counter').val();
            row++;
            var str = '<div class="form-group"><label for="inputEmail">' + row + 'th day value</label><input type="number" step="any" class="form-control" name="open[]" placeholder="Open Value" required><input type="number" step="any" class="form-control" name="high[]" placeholder="High Value" required><input type="number" step="any" class="form-control" name="low[]" placeholder="Low Value" required><input type="number" step="any" class="form-control" name="close[]" placeholder="Close Value" required></div>';
            $('#extra_div').append(str);
            $('#counter').val(row);
        }
        function removeDay() {
            if ($('#extra_div').find('.form-group').length == 0) {
                alert('You dont have any removal row');
                return false;
            }
            if (confirm('Are you sure ?') == true) {
                var row = $('#counter').val();
                row--;
                $('#extra_div').find('.form-group').last().remove();
                $('#counter').val(row);
            }
        }
    </script>

    <?php require_once 'elements/footer.php'; ?>

</body>

</html>


