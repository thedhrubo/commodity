<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

    <?php require_once 'elements/head.php'; ?>
    <style>
        .form-group{
            background-color: #e8e8e8;
            margin: 10px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
    <body>
        <?php require_once 'elements/header.php'; ?>

    <section id="services" class="section section-padded">

        <div class="col-md-6 col-md-offset-3">
            <h3>Settings</h3>
            <form method="post">
                <div class="form-group">
                    <label>Minimum Percentage Value will show into evaluation report :</label>
                    <input type="number" placeholder="Minimum Percentage" value="<?php echo $min_percent; ?>" name="min_percent" class="form-control">
                </div>
                <div class="form-group">
                    <input type="submit" style="width: 100%;" class="btn btn-blue" value="Save">
                </div>
            </form>
        </div>
        <div class="clearfix"></div>
    </section>


    <?php require_once 'elements/footer.php'; ?>

</body>

</html>



