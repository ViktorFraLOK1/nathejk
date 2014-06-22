<?php
/**
 * @package Sbs
 */
class Nathejk_Page extends Pasta_Page
{
    public function __construct($fancybox = false)
    {
        parent::__construct();

        if ($fancybox) {
            $this->headerTemplatePath = 'fancybox/header.tpl';
            $this->footerTemplatePath = 'fancybox/footer.tpl';
        }
    }



    public function initialize()
    {
        $q = new Nathejk_Agenda;
        $this->assign('agenda', $q->findOne());
        global $USER;
        if ($USER) {
            $query = new Nathejk_Klan;
            $query->deletedUts = 0;
            $query->columnIn('typeName', array('super', 'klan'));
            $query->addWhereSql('title != ""');
            $klanCount = $query->countAll();
            $query->signupStatusTypeName = Nathejk_Team::STATUS_PAID;
            $activeKlanCount = $query->countAll();
            $pendingKlanCount = $klanCount - $activeKlanCount;
            $this->assign(compact('klanCount', 'activeKlanCount', 'pendingKlanCount'));
            
            $query = new Nathejk_Patrulje;
            $query->deletedUts = 0;
            $query->typeName = 'patrulje';
            $query->addWhereSql('title != ""');
            $patruljeCount = $query->countAll();
            $query->signupStatusTypeName = Nathejk_Team::STATUS_PAID;
            $activePatruljeCount = $query->countAll();
            $pendingPatruljeCount = $patruljeCount - $activePatruljeCount;
            $this->assign(compact('patruljeCount', 'activePatruljeCount', 'pendingPatruljeCount'));
            
            $query = new Nathejk_CheckIn;
            $query->addWhereSql('typeName != "qr-fail"');
            $this->assign('checkInCount', $query->countAll());

            $query = new Nathejk_Photo;
            $query->teamId = 0;
            $query->memberId = 0;
            $this->assign('photoCount', $query->countAll());
            /*
            // Get the saved searches
            $savedSearchesTable = new Sbs_Search_Saved();
            $savedSearchesTable->setOrderBySql('title ASC');
            $savedSearches = $savedSearchesTable->findAll();
            $this->assign('savedSearches', $savedSearches);
            
            // Setup columns views for select boxes in header
            $this->assign('columnViewNames', Sbs_DisplayColumns::getViewNames());
            */

            if ($USER->username == 'post' && isset($_COOKIE['post'])) {
                $q = new Nathejk_Senior;
                $q->id = $_COOKIE['post'];
                $this->assign('post', $q->findOne());
            }
        }
        parent::initialize();
    }
}

?>
