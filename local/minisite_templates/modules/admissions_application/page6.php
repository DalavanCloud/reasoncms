<?php

/**
 * Admissions Application Module
 *
 * @author Steve Smith
 * @author Lucas Welper
 * @since 2011-02-11
 * @package ControllerStep
 *
 */
/*
 *  Sizth page of the application
 *
 *  Misc Information
 *          College Plan
 *          Influences
 *          Other Colleges Applied to
 *          Personal Statement
 *          Criminal History
 *          More????
 */
class ApplicationPageSix extends FormStep {

    var $_log_errors = true;
    var $error;
    var $majors_array = array(
        'ACCTG' => 'Accounting',
        'AFRS' => 'Africana Studies',
        'ANTH' => 'Anthropology/Archaeology',
        'ARCH' => 'Architecture',
        'ART' => 'Art',
        'ARTM' => 'Art Management',
        'ATHT' => 'Athletic Training',
        'BIBL' => 'Biblical Languages',
        'BIOC' => 'Biochemistry',
        'BIOC' => 'Biology',
        'BIOE' => 'Biology (Environmental)',
        'CHEM' => 'Chemistry',
        'CLAST' => 'Classical Studies',
        'COMM' => 'Communication',
        'CS' => 'Computer Science',
        'ECON' => 'Economics',
        'EDUC' => 'Education',
        'EDEL' => 'Education-Elemenatary',
        'EDSE' => 'Education-Secondary',
        'EDSP' => 'Education-Special',
        'ENGL' => 'English',
        'ENVS' => 'Environmental Studies',
        'FINA' => 'Fine Arts',
        'FREN' => 'French',
        'GER' => 'German',
        'GRDE' => 'Graphic Design',
        'GRK' => 'Greek',
        'HLTH' => 'Health',
        'HIST' => 'History',
        'INTS' => 'International Management',
        'IS' => 'International Studies',
        'JOUR' => 'Journalism',
        'LAT' => 'Latin',
        'MGT' => 'Management',
        'MIS' => ' Management Info Systems',
        'MATH' => '	Mathematics',
        'MSTAT' => ' Mathematics/Statistics',
        'MEDT' => ' Medical Technology',
        'MLAN' => ' Modern Languages',
        'MUST' => 'Museum Studies',
        'MUS' => 'Music',
        'MUSE' => 'Music Education',
        'MUSM' => 'Music Management',
        'MUSP' => 'Music Performance',
        'NSCI' => 'Natural Science',
        'NURS' => 'Nursing',
        'PHIL' => 'Philosophy',
        'PE' => 'Physical Education',
        'PTOT' => 'Physical/Occ Therapy',
        'PHYS' => 'Physics',
        'POLS' => 'Political Science',
        'PDEN' => 'Pre-dental',
        'PENG' => 'Pre-engineering',
        'PFOR' => 'Pre-forestry',
        'PLAW' => 'Pre-law',
        'PMED' => 'Pre-medicine',
        'POPT' => 'Pre-optometry',
        'PPHA' => 'Pre-pharmacy',
        'PPT' => 'Pre-physical therapy',
        'PSEM' => 'Pre-seminary',
        'PVET' => 'Pre-veterinary',
        'PSYB' => 'Psychobiology',
        'PSYC' => 'Psychology',
        'REL' => 'Religion',
        'RUST' => 'Russian Studies',
        'SCST' => 'Scandanavian Studies',
        'SSCI' => 'Social Science',
        'SW' => 'Social Work',
        'SOC' => 'Sociology',
        'SOPO' => 'Soc/Political Science',
        'SPAN' => 'Spanish',
        'SPMT' => 'Sports Management',
        'THD' => 'Theatre/Dance',
        'THDM' => 'Theatre/Dance Management',
        'UND' => 'Deciding',
        'WOMS' => 'Women\'s Studies',
    );
    var $elements = array(
        'college_plan_comment' => array(
            'type' => 'comment',
            'text' => '<h3>College Plan</h3>',
        ),
        'college_plan_1' => array(
            'type' => 'select',
            'display_name' => 'First Choice',
            'options' => array(),
        ),
        'college_plan_2' => array(
            'type' => 'select',
            'display_name' => 'Second Choice',
            'options' => array()
        ),
        'music_audition_comment' => array(
            'type' => 'comment',
            'text' => 'Do you intend to audition for a music scholarship?',
        ),
        'music_audition' => array(
            'type' => 'radio_inline_no_sort',
            'display_name' => '&nbsp;',
            'options' => array('Yes' => 'Yes', 'No' => 'No'),
        ),
        'instrument_comment' => array(
            'type' => 'comment',
            'text' => 'If yes, with which instrument will you audition?',
        ),
        'music_audition_instrument' => array(
            'type' => 'select_no_sort',
            'display_name' => '&nbsp;',
            'add_null_value_to_top' => true,
            'options' => array(
                'Flute' => 'Flute',
                'Oboe' => 'Oboe',
                'Clarinet' => 'Clarinet',
                'Bassoon' => 'Bassoon',
                'Saxophone' => 'Saxophone',
                'Horn' => 'Horn',
                'Trumpet' => 'Trumpet',
                'Trombone' => 'Trombone',
                'Tuba' => 'Tuba',
                'Piano' => 'Piano',
                'Organ' => 'Organ',
                'Harp' => 'Harp',
                'Harpsichord' => 'Harpsichord',
                'Guitar' => 'Guitar',
                'Percussion' => 'Percussion',
                'Violin' => 'Violin',
                'Viola' => 'Viola',
                'Cello' => '\'Cello',
                'Double Bass' => 'Double Bass',
                'Voice' => 'Voice',
            ),
        ),
        'financial_aid_comment' => array(
            'type' => 'comment',
            'text' => 'Do you plan to apply for financial aid?',
        ),
        'financial_aid' => array(
            'type' => 'radio_inline_no_sort',
            'display_name' => '&nbsp;',
            'options' => array('Yes' => 'Yes', 'No' => 'No'),
        ),
        'influences_comment' => array(
            'type' => 'comment',
            'text' => 'Please list the influences that led you to Luther College'
        ),
        'influences' => array(
            'type' => 'textarea_no_label',
        ),
        'other_colleges_comment' => array(
            'type' => 'comment',
            'text' => 'I have applied or intend to apply to the following colleges'
        ),
        'other_colleges' => array(
            'type' => 'textarea_no_label',
        ),
        'personal_statment_header' => array(
            'type' => 'comment',
            'text' => '<h3>Personal Statement</h3>',
        ),
        'personal_statement_instructions' => array(
            'type' => 'comment',
            'text' => 'In at least 250 words, please describe an activity, interest, experience or achievement in your life
                  that has been particularly meaningful to you. Please note that you may mail a graded paper to our Office of Admissions
                  in leiu of submitting a personal statement'
        ),
//        'personal_statement' => array(
//            'type' => 'upload',
//            'acceptable_types' => array('application/pdf', 'image/*'),
//            'display_name' => 'Upload a file',
//            'original_path' => '/tmp/',
//            'allow_upload_on_edit'
//        ),
        'personal_statement' => array(
            'type' => 'textarea_no_label',
        ),
        'disciplinary_header' => array(
            'type' => 'comment',
            'text' => '<h3>Disciplinary History</h3>',
        ),
        'conviction_history_comment' => array(
            'type' => 'comment',
            'text' => 'Have you ever been convicted of a misdemeanor, felony, or other crime?',
        ),
        'conviction_history' => array(
            'type' => 'radio_inline_no_sort',
            'display_name' => '&nbsp;',
            'options' => array('Yes' => 'Yes', 'No' => 'No'),
        ),
        'conviction_details_comment' => array(
            'type' => 'comment',
            'text' => 'If yes, please describe',
        ),
        'conviction_history_details' => array(
            'type' => 'textarea_no_label',
            'display_name' => '&nbsp;',
        ),
        'hs_discipline_comment' => array(
            'type' => 'comment',
            'text' => 'Have you ever been found responsible for a discplinary violation while attending high school that resulted in probation,
                  suspension, dismissal, or expulsion?',
        ),
        'hs_discipline' => array(
            'type' => 'radio_inline_no_sort',
            'display_name' => '&nbsp;',
            'options' => array('Yes' => 'Yes', 'No' => 'No'),
        ),
        'discipline_details_comment' => array(
            'type' => 'comment',
            'text' => 'If yes, please describe',
        ),
        'hs_discipline_details' => array(
            'type' => 'textarea_no_label',
            'display_name' => '&nbsp;',
        ),
//        'permission_for_transcripts' => array(
//            'type' => 'checkboxfirst',
//            'display_name' => 'Checking this box indicates that I grant my high school permission to release my transcript and test scores,
//                  if available, directly to Luther College.'
//        ),
        'honesty_statement' => array(
            'type' => 'checkboxfirst',
            'display_name' => 'Checking this box indicates that all information in my application is complete, factually correct, and honestly
                  presented.'
        ),
    );
    var $display_name = 'Last Page';
    var $error_header_text = 'Please check your form.';

    function on_every_time() {
        $this->change_element_type('college_plan_1', 'select', array('options' => $this->majors_array));
        $this->change_element_type('college_plan_2', 'select', array('options' => $this->majors_array));
    }

    function pre_fill_form() {
        // check if the open_id has is set
        $o_id = check_open_id($this);
        if ($o_id) {
            // get an existing users data from the db based on openid_id and the form
            get_applicant_data($o_id, $this);
        } else {
            // no show form, invite to login
            $this->show_form = false;
        }
    }

    function pre_show_form() {
        echo '<div id="admissionsApp" class="pageSix">' . "\n";
    }

    function post_show_form() {
        echo '</div>' . "\n";
    }

    function process() {
        parent::process();
        set_applicant_data($this->openid_id, $this);
    }

    function get_safer_filename($filename) {
        // returns a "safe" filename with .txt added to unsafe extensions - nwhite 12/12/05
        $unsafe_to_safer = array(
            'py' => 'py.txt',
            'php' => 'php.txt',
            'asp' => 'asp.txt',
            'aspx' => 'aspx.txt',
            'pl' => 'pl.txt',
            'shtml' => 'shtml.txt',
            'cfm' => 'cfm.txt',
            'woa' => 'woa.txt',
            'php3' => 'php3.txt',
            'jsp' => 'jsp.txt',
            'js' => 'js.txt',
            'exe' => 'exe.txt',
            'cgi' => 'cgi.txt',
            'vb' => 'vb.txt',
            'bat' => 'bat.txt',
        );
        list($filename, $fext) = $this->_get_filename_parts($filename);
        if (!empty($unsafe_to_safer[$fext]))
            $fext = $unsafe_to_safer[$fext];
        if (!empty($fext))
            $filename .= '.' . $fext;
        return $filename;
    }

    function _get_filename_parts($filename) {
        $parts = explode('.', $filename);

        if (count($parts) <= 1) {
            return array(basename($filename), '');
        } else {
            $extension = array_pop($parts);
            return array(basename($filename, ".$extension"), $extension);
        }
    }

    function post_error_check_actions() { // {{{
        // display the URL of the document or a warning if no doc dir is set up.
        $asset = $this->get_element('personal_statement');
        $site = new entity($this->get_value('site_id'));
        if ($this->get_value('file_name')) {
            if ($this->has_error('asset') OR $this->has_error('file_name'))
                $text = 'Document URL: Cannot be determined until errors are resolved.';
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $site->get_value('base_url') . MINISITE_ASSETS_DIRECTORY_NAME . '/' . $this->get_value('file_name');
            $text = 'Document URL: ';
            if ($asset->state == 'existing' && $this->get_value('state') == 'Live' && !$this->_has_errors()) {
                $text .= '<a href="' . $url . '" target="_new">' . $url . '</a>';
            } elseif ($this->_has_errors()) {
                $text .= $url . ' (link may not work until errors are resolved)';
            } else {
                $text .= $url . ' (will be live once saved)';
            }
            $this->add_element('doc_url', 'comment', array('text' => $text));
        }
    }

    function where_to() {
//        $refnum = $this->get_value('result_refnum');
//        $text = $this->get_value('confirmation_text');
//        reason_include_once('minisite_templates/modules/gift_form/gift_confirmation.php');
//        $gc = new GiftConfirmation;
//        $hash = $gc->make_hash($text);
//        connectDB(REASON_DB);
//        $url = get_current_url();
//        $parts = parse_url($url);
//        $url = $parts['scheme'] . '://' . $parts['host'] . $parts['path'] . '?r=' . $refnum . '&h=' . $hash;
        $url = 'http://www.luther.edu';
        return $url;
    }
}

?>