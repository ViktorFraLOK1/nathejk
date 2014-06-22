<div class="row" style="display:none">
    <div class="span5">
        <div class="row">
            <div class="span2" style="background:yellow">
                <ul class="thumbnails">
                    <li class="span2">
                        <span class="thumbnail"><img src="{$member->photoUrl|escape}" alt=""></span>
                    </li>
                </ul>
            </div>
            <div class="span3" style="background:green">
                kolonne 2
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="span5">
        <h3 id="memberTitle{$member->id|escape}">{$member->title|escape}</h3>
        <script type="text/javascript">
        $('#memberTitle{$member->id|escape}').html($('#memberTitleInput{$member->id|escape}').val());
        </script>
        <div class="row fileupload fileupload-new" data-provides="fileupload">

            <div class="span2" style="margin-bottom:0px">
                <ul class="thumbnails" style="margin-bottom:0px">
                    <li class="span2">
    <span class="fileupload-new thumbnail" style="width: 160px; height: 160px;"><img src="{$member->photoUrl|escape}" /></span>
    <span class="fileupload-preview fileupload-exists thumbnail" style="max-width: 160px; max-height: 160px; line-height: 20px;"></span>
                    </li>
                </ul>
            </div>
            <div class="span3">
                <p>Upload vellignende billede som opfylder, de i <a href="https://www.retsinformation.dk/Forms/R0710.aspx?id=2404">BEK nr 1003 af 06/10/2006 §6 stk. 2</a> gældende krav til billede, som ses på politiets <a href="https://www.politi.dk/NR/rdonlyres/5767C5D1-35B4-497B-BC71-F7669B0934F9/0/pasplakat_0109.jpg">eksempel</a>.</p>
                <span class="btn btn-file"><span class="fileupload-new">Vælg billede</span><span class="fileupload-exists">Skift billede</span><input type="file" name="photos[{$member->id|escape}]"/></span>
            </div>
        </div>
    </div>
</div>
