<nav class="toolTableTools">
    <ul>
        {* if $toolbarEnableRemoveFromClipboard}
            <li><a href="/clipboard/remove.php" class="fancybox600x480 confirm icon16x16clipboard">remove from clipboard</a></li>
        {else}
            <li><a href="/clipboard/add.php" class="fancybox600x480 confirm icon16x16clipboard">add to clipboard</a></li>
        {/if}
        <li><a href="/functions/import.php" class="fancybox600x480 confirm icon16x16thumbsup" title="import">import</a></li>
        <li><a href="/functions/merge.php" class="fancybox600x480 confirm icon16x16merge" title="merge">merge</a></li>
        <li><a href="/functions/combine/" class="fancyboxfull confirm icon16x16combine" title="combine">combine</a></li>
        <li><a href="/functions/link/" class="fancyboxfull confirm icon16x16link" title="link">link</a></li>
        <li><a href="/functions/unlink.php" class="fancybox600x480 confirm icon16x16unlink">unlink</a></li>
        *}
        {if $USER->username eq 'nathejk'}
        <li><a href="functions/delete.php" class="fancybox600x480 confirm icon16x16delete">slet</a></li>
        <li><a href="functions/mail.php" class="fancyboxFullxFull confirm icon16x16emailedit">send e-mail</a></li>
        <li><a href="functions/pay.php" class="fancyboxFullxFull confirm icon16x16money">indbetalinger</a></li>
        <li><a href="functions/export.php" class="fancybox600x480 confirm icon16x16export">eksporter</a></li>
        {/if}

        {* if $USER->username eq 'post' *}
        <li><a href="functions/checkin.php" class="fancyboxFullxFull confirm icon16x16thumbsup">check ind/ud</a></li>
        {* /if *}
        
        {*
        <li><a href="/functions/edit.php" class="fancyboxfull confirm icon16x16edit" title="quick edit">quick edit</a></li>
        <li><a href="/functions/multiEdit.php" class="fancyboxfull confirm icon16x16batchedit" title="multi edit">multi edit</a></li>
        *}
    </ul>
</nav>
