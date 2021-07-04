<?php

function irp_ui_feedback() {
    global $irp;

    $irp->Form->prefix='Feedback';
    if($irp->Check->nonce('irp_feedback', 'irp_feedback')) {
        $irp->Check->email('email');
        $irp->Check->value('body');

        if(!$irp->Check->hasErrors()) {
            $irp->Options->setFeedbackEmail($irp->Check->of('email'));
            $id=-1;
            if($irp->Check->of('track', 0)) {
                $id=$irp->Tracking->sendTracking(TRUE);
            }
            $irp->Check->data['tracking_id']=$id;
            $irp->Check->data['plugin']=IRP_PLUGIN_SLUG;
            $data=$irp->Utils->remotePost('feedback', $irp->Check->data);
            if($data) {
                $irp->Options->pushSuccessMessage('FeedbackSuccess');
            } else {
                $irp->Options->pushErrorMessage('FeedbackError');
            }
        }
    }
    ?>
    <br>
    <h2><?php $irp->Lang->P('FeedbackHeader')?></h2>
    <?php
    $irp->Options->writeMessages();

    $irp->Form->formStarts();
    $irp->Form->text('email', $irp->Options->getFeedbackEmail());
    $irp->Form->textarea('body', '', array('rows'=>5));

    $irp->Form->leftLabels=FALSE;
    $irp->Form->checkbox('track');
    $irp->Form->leftLabels=TRUE;

    $irp->Form->nonce('irp_feedback', 'irp_feedback');
    $irp->Form->submit('Send');
    $irp->Form->formEnds();
}