<?php $this->assign('title', 'Help | ' . h($currentEntity['portal_title'])); ?>

<div class='container inner-navbar'>
    <div class="logo col-md-3">
        <a href='/'><img class='logo-image' src="/img/brand.png"></a>
    </div>
    <div class="outer-tab-panel col-md-9">
        <?php
        if ($authedUser) {
            echo $this->element('primary_nav', ["active" => ""]);
        } // Include primary navbar element  
        ?>
    </div>
</div>

<div class="actual-content">
    <div class="container help-page-container">
        <div class='row body-part tab-content inner-tab-content'>
            <div class='col-md-12'>
                <h2>HELP</h2>
                <p>Below, find videos and instructions for using the features of this site.  If you cannot find the answer to your question or believe the site might not be functioning properly, contact <a href="mailto:feast@cgiar.org">feast@cgiar.org</a>.</p>
                <p>Click to expand a topic.</p>
                <div id='general-help'>
                    <div class="help-control">
                        <a role="button" data-toggle="collapse" href="#general-help-contents" aria-expanded="false" aria-controls="general-help-contents">BROWSING THE REPOSITORY AND CREATING AN ACCOUNT
                    </div>
                    <div id="general-help-contents" class="collapse">
                        <iframe src="http://player.vimeo.com/video/181713239" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                        <p>Welcome to this tutorial on navigating the FEAST Global Data Repository website.</p>
                        <p>This website provides a central repository of data related to the livestock feed resources of smallholder farming communities, collected by users of the International Livestock Research Institute’s FEAST Data Application.</p>
                        <p>There are several areas that visitors to the site may explore without having to register for an account, including the home page, which includes a news feed with stories related to ILRI and the FEAST program, the &ldquo;Downloads&rdquo; page where visitors may see a list of data collection projects, organized by world region and country, and the &ldquo;Reports&rdquo; page, where visitors may view charts and graphs generated from the repository data.</p>
                        <p>If you wish to download a copy of the FEAST Data Application, upload data or download the complete repository data set for analysis, then you will need to register for an account.</p>
                        <p>To create an account, click the &ldquo;Register&rdquo; button in the right-hand column of the home page.  Fill in the required data on the registration form - including your name, email address, organization and password - then click &ldquo;register&rdquo;.</p>
                        <p>After registering, you will receive an automatically-generated email with a confirmation link.  After you click the confirmation link, you will be able to log in as a registered user by entering your email address and password into the sign-in form.</p>
                    </div>
                </div>
                
                <div id="download-help">
                    <div class="help-control">
                        <a role="button" data-toggle="collapse" href="#download-help-contents" aria-expanded="false" aria-controls="download-help-contents">DOWNLOADING DATA</a>
                    </div>
                    <div id="download-help-contents" class="collapse">
                        <iframe src="http://player.vimeo.com/video/181713336" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                        <p>To download the repository data for analysis in your preferred software - such as Excel - you first need to register for an account.</p>
                        <p>After signing in with your username and password, navigate to the &ldquo;Download Data Sets&rdquo; tab on the &ldquo;Download&rdquo; page.</p>
                        <p>From here you have three options.</p>
                        <p>The first option is to download all of the repository data in CSV format.</p>
                        <p>The second option is to download CSV files containing only data that you, personally, uploaded to the aggregator site.</p>
                        <p>When downloading CSVs, you also have the option to filter the data set by world region, country, project and/or site.</p>
                        <p>CSV downloads will be saved to your local hard drive as a ZIP file.  Use an unzip utility to extract the individual CSV files, which can then be opened in Excel or any other tool you might prefer.</p>
                        <p>If you have questions about what the various columns mean, you can download a PDF document with detailed descriptions of the fields included in each CSV file.  This document is also available on the &ldquo;Feed Assessment Tool&rdquo; tab of the &ldquo;Download&rdquo; page.</p>
                        <p>The third option for downloading data is to download the entire repository as a SQLite database file.  If you know how to work with SQL databases, then this might be a more convenient option as relationships between the database tables will remain intact.</p>
                        <p>Filtering does <strong>not</strong> apply when downloading a SQLite database file.</p>
                    </div>
                </div>

                <div id="upload-help">
                    <div class="help-control">
                        <a role="button" data-toggle="collapse" href="#upload-help-contents" aria-expanded="false" aria-controls="upload-help-contents">UPLOADING DATA</a>
                    </div>
                    <div id="upload-help-contents" class="collapse">
                        <iframe src="http://player.vimeo.com/video/181713394" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                        <p>To upload data exported from the FEAST Data Application, sign into your account then navigate to the &ldquo;Upload&rdquo; tab on the &ldquo;My Data&rdquo; page.</p>
                        <p>From here, either click on the gray box labeled &ldquo;Click or drag a file here to upload&rdquo; then select your ZLIB file exported from the FEAST Data Application, or else drag a ZLIB file from your desktop and drop it into the box.</p>
                        <p>If you do not know how to export a data file from the FEAST Data Application, refer to the user manual included with the application or visit the ILRI Learning Portal, accessible via a link on the &ldquo;Help and Tutorials&rdquo; page, to access e-learning modules and tutorials on the FEAST Data App.</p>
                        <p>Once a file has been selected, the name of the file will appear below the gray box. Click the &ldquo;Upload&rdquo; button to complete the process.</p>
                        <p>You may tick the &ldquo;Keep my data private for 1 year&rdquo; box if you do not wish to share your data immediately with the community.  However, be advised that all data uploaded to the site will become available to the community after one year.  You can read more about ILRI’s open data policy on the FEAST website.</p>
                        <p>When you are ready to share data that had previously been kept private, navigate to the &ldquo;Data Privacy&ldquo; tab on the &ldquo;Upload&rdquo; page. If you opted to keep data from any of your projects private, they will be listed on this page. To make data public, tick the box next to the project then click &ldquo;Make Public&rdquo;.</p>
                        <p>Note that ALL data from the selected project will be made public. It is not possible to selectively make some data from a project public and other data private.</p>
                        <p>Also, once data has been made public, it cannot be made private again.</p>
                    </div>
                </div>
                
                <div id="exclude-help">
                    <div class="help-control">
                        <a role="button" data-toggle="collapse" href="#exclude-help-contents" aria-expanded="false" aria-controls="exclude-help-contents">DATA CLEANUP: EXCLUDING RECORDS</a>
                    </div>
                    <div id="exclude-help-contents" class="collapse">
                        <iframe src="http://player.vimeo.com/video/181713470" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                        <p>After you upload data, there might be records that you wish to exclude from the database for one reason or another.  Perhaps you are not fully confident in the integrity of the data or feel the data might be misleading to people who do not know the full context of the project.</p>
                        <p>If this is the case, you can exclude records by navigating to the &ldquo;Manage My Data&rdquo; tab on the &ldquo;My Data&rdquo; page.</p>
                        <p>Using the drop-down, select the table containing the record you wish to exclude.  Note that tables in the list marked with an asterisk are look-up tables referenced by other tables: records from these tables cannot be excluded, as doing so might invalidate data in related tables.</p>
                        <p>Once you have selected a table, you may filter the records by any of the columns listed in the &ldquo;filter&rdquo; drop-down.  Select a column, type in a value, then click &ldquo;add&rdquo; to apply the filter.  You may apply as many filters as you like.</p>
                        <p>From the list of records, tick the &ldquo;select&rdquo; box on the right-hand side of the table for any and all records you wish to exclude.  Then select &ldquo;Toggle Exclude&rdquo; from the &ldquo;Bulk Actions&rdquo; drop-down and click &ldquo;Submit&rdquo;.</p>
                        <p>This will cause any selected records and their child records from other tables to be excluded from the database.  So, for example, if you exclude a project, then any related sites, focus groups and interview respondents will also be excluded.</p>
                        <p>To reinstate an excluded record, simply select it from the list, then choose &ldquo;Toggle Exclude&rdquo; and &ldquo;Submit&rdquo; again.  The records that had been excluded will be included again in any reports and downloads.</p>

                    </div>
                </div>
                
                <div id="consolidate-help">
                    <div class="help-control">
                        <a role="button" data-toggle="collapse" href="#consolidate-help-contents" aria-expanded="false" aria-controls="consolidate-help-contents">DATA CLEANUP: CONSOLIDATING RECORDS</a>
                    </div>
                    <div id="consolidate-help-contents" class="collapse">
                        <iframe src="http://player.vimeo.com/video/181713494" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                        <p>After you upload data, you might discover that some of the custom records you created for crops, livestock types, units of measurement and other data might already be represented in the database.</p>
                        <p>In these cases, it might be useful to consolidate the records, so that - for example - someone searching for data on &ldquo;yaks&rdquo;, &ldquo;cavies&rdquo; or &ldquo;reindeer&rdquo; based on someone else’s custom livestock types will be able to see your data for those types of animals as well.</p>
                        <p>To consolidate records, sign in and navigate to the &ldquo;Manage My Data&rdquo; tab on the &ldquo;My Data&rdquo; page.</p>
                        <p>Using the drop-down, select the table containing the record you wish to consolidate.  Note that only tables in the list marked with an asterisk allow records to be consolidated.</p>
                        <p>Once you have selected a table, you may filter the records by any of the columns listed in the &ldquo;filter&rdquo; drop-down.  Select a column, type in a value, then click &ldquo;add&rdquo; to apply the filter.  You may apply as many filters as you like.</p>
                        <p>From the list of records, tick the &ldquo;select&rdquo; box on the right-hand side of the table for any and all records that you wish to replace with a different record from the table.  Then select &ldquo;Consolidate&rdquo; from the &ldquo;Bulk Actions&rdquo; drop-down and click &ldquo;Submit&rdquo;.</p>
                        <p>A form will appear listing all of the other records in the table, with an option to filter records if desired.</p>
                        <p>Find the record that you would like to serve as a replacement for the selected records and click the &ldquo;Replace with This&rdquo; button on the right-hand side of that row.</p>
                        <p>This will cause any selected records to redirect to the new record.  The ID of the replacement record will appear in the &ldquo;Replaced by&rdquo; column for the consolidated records.</p>
                        <p>To undo the consolidation, simply select a consolidated record, choose &ldquo;Consolidate&rdquo; from the bulk actions drop-down then click &ldquo;Submit&rdquo;.  You can then have the record point to itself - so, for instance, if you previously consolidated record 2 into 1 and wish to undo the consolidation, just click the &ldquo;Replace with this&rdquo; button for record 2 on the list.</p>
                        <p>If you do this, then the other number will disappear from the &ldquo;replaced by&rdquo; column and any reports or searches will once again refer to the original record.</p>
                    </div>
                </div>
                
                <div id="report-help">
                    <div class="help-control">
                        <a role="button" data-toggle="collapse" href="#report-help-contents" aria-expanded="false" aria-controls="report-help-contents">VIEWING REPORTS</a>
                    </div>
                    <div id="report-help-contents" class="collapse">
                        <iframe src="http://player.vimeo.com/video/181713506" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                        <p>On the &ldquo;Reports&rdquo; page, you may view graphs and charts based on data from the repository.  These standard reports were designed to provide quick access to commonly requested data.  If you would like a complete set of repository data to analyze in your preferred software, you can register for an account on the home page then download a zip file of the complete data set from the &ldquo;Downloads&rdquo; page.</p>
                        <p>Returning to our standard reports, use the drop-down menus to select a report, and the relevant graph will then appear below the drop-down menus.</p>
                        <p>You may choose to group the report data by country, world region, gender, project or site.  Once you make a selection, the graph will automatically refresh.</p>
                        <p>Next, you may optionally filter the data based on the same set of variables -  gender, project, site, world region and country.</p>
                        <p>Finally, if you have marked any of your personal data as &ldquo;private&rdquo;, you may choose whether to include or exclude your private data in the report.</p>
                        <p>A CSV file with the current chart data may be downloaded by clicking the &ldquo;Get Source Data&rdquo; link.</p>
                        <p>If you would like to compare two sets of data, click the &ldquo;Add Second Report&rdquo; button.  A second set of drop-down menus will appear, allowing you to group and filter data for a second chart, which will appear below the first chart on the Reports page.</p>
                    </div>
                </div>

                <div id="usage-help">
                    <div class="help-control">
                        <a role="button" href="/help/usage">USING THE FEAST DATA APPLICATION</a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<?php $this->Html->script('help.js', array('block' => 'script')) ?>
