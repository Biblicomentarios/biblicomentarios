<?php
function irp_ui_faq() {
    global $irp;
    $i=1;
    while($irp->Lang->H('Faq.Question'.$i)) {
        $q=$irp->Lang->L('Faq.Question'.$i);
        $r=$irp->Lang->L('Faq.Response'.$i);
        ?>
        <p>
            <b><?php echo $q?></b>
            <br/>
            <?php echo $r?>
        </p>
        <?php
        ++$i;
    }
}