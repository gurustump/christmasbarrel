<div class="wrap">
        <?php echo html('h2', __('Overview','voting-contest')); ?>
		<style>
			.overview-page ul{list-style:none;}
			.overview-page ul li ul {list-style: none;padding-left:30px;}
			.overview-page ul li b{font-size:15px;}
			.overview-page ul li{line-height:20px;}
			.overview-page ul ul li{background:url(<?php echo VOTES_PATH;?>/images/arrow.jpg) no-repeat left center;padding-left:15px}
			ul.overview-listing{margin-left:25px;}
		</style>
            <div class="narrow overview-page" id="accordion">
                <h1><?php _e('Version','voting-contest'); ?> 2.5.2 |  <a href="http://plugins.ohiowebtech.com/documentation/setup-contest-category/" target="_blank"><?php _e('Online Documentation','voting-contest'); ?></a>  |  <a href="http://voting.ohiowebtech.com/" target="_blank"><?php _e('Demo Contests','voting-contest'); ?></a></h1>
		<h2><?php _e('Example of all shortcode usage:','voting-contest'); ?><br />[showcontestants id=42 postperpage=20 thumb=1 height=200 width=200 title=Sample termdisplay=1 order=ASC orderby=date showtimer=1 showform=1 view=grid pagination=0]</h2>
				<ul class="overview-listing">
				<li ><b><u>showcontestants</u></b><br/><p><i><?php _e('Attributes that can be passed to this shortcode are as follows:','voting-contest'); ?></i></p>
					<ul>
						<?php
						echo html('li', '<u>orderby</u> &nbsp'.__('(Note: To specify the Orderby of the Contestant Listing)','voting-contest'));
						echo html('li', '<u>order</u> &nbsp '.__('(Note: To specify the Order of the Contestant Listing)','voting-contest'));
						echo html('li', '<u>postperpage</u> &nbsp '.__('(Note: To specify the Number of Contestants displayed per page in the Contest Listing)','voting-contest'));
						echo html('li', '<u>id</u> &nbsp (Note: To specify the Category of the post from which the Contestants get displayed in the Contest Listing)'.__('(Note: To specify the Order of the Contestant Listing)','voting-contest'));
						echo html('li', '<u>thumb</u> &nbsp '.__('(Note: To specify whether the thumbnail want to get displayed in the Contest Listing)','voting-contest'));
						echo html('li', '<u>height</u> &nbsp '.__('(Note: To specify the Height of the Thumbnail in the Contest Listing)','voting-contest'));
						echo html('li', '<u>width</u> &nbsp '.__('(Note: To specify the Width of the Thumbnail in the Contest Listing)','voting-contest'));
						echo html('li', '<u>title</u> &nbsp '.__('(Note: To specify the Title of the Contest Listing)','voting-contest'));
						echo html('li', '<u>termdisplay</u> &nbsp '.__('(Note: To specify whether the Category is displayed in the Contest Listing)','voting-contest'));
                        echo html('li', '<u>exclude</u> &nbsp '.__('(Note: To exclude the specified Contests in the Contest Listing, ids separated by comma)','voting-contest'));
                        echo html('li', '<u>pagination</u> &nbsp '.__('(Note: The contestants will be displayed without pagination if it is set to 0)','voting-contest'));
						echo html('li', '<u>onlyloggedinuser</u> &nbsp '.__('(Note: To specify Whether the non-loggedin Users able to cast the vote or not.)','voting-contest'));
						echo html('li', '<u>showtimer</u> &nbsp '.__('(Note: To specify Whether the Start or End timer should be displayed or Not.)','voting-contest'));
						echo html('li', '<u>showform</u> &nbsp '.__('(Note: To specify Whether the Add Contestant form should be displayed or Not.)','voting-contest'));
                        echo html('li', '<u>view</u> &nbsp '.__('(Note: The contestants will be displayed both in grid and list by default. you can specify the grid/list using this shortcode)','voting-contest'));
                        
						?>
					</ul>
				</li>
				
				<li ><b><u><?php _e('Multiple Contestants In Single Page','voting-contest')?></u></b><br/><p><i><?php _e('Create a page and use showcontestant shortcode multiple times. It will list multiple contestants in a single page.','voting-contest'); ?></i></p>
					<ul>
						<?php
						echo html('li', '<u>Example</u> &nbsp (Note: [showcontestants id=3 showform=1][showcontestants id=2 showform=1] etc)');
						echo html('li', '<u>Use</u> &nbsp '.__('(Note: you can use the attributes available for show contestant shortcode.)','voting-contest'));
						?>
					</ul>
				</li>
				
				
				<li><b><u>upcomingcontestants</u></b><br/><p><i><?php _e('Attributes that can be passed to this shortcode are as follows:','voting-contest'); ?></i></p>
					 <ul>
						<?php
						echo html('li', '<u>id</u> &nbsp '.__('(Note: To specify the Category of the post for which the Start timer get displayed.)','voting-contest'));
						echo html('li', '<u>showcontestants</u> &nbsp '.__('(Note: To specify Whether the Contestants get displayed or not.)','voting-contest'));
						?>
					</ul>
				</li>
				<li><b><u>endcontestants</u></b><br/><p><i><?php _e('Attributes that can be passed to this shortcode are as follows:','voting-contest'); ?></i></p>
					 <ul>
						<?php
						echo html('li', '<u>id</u> &nbsp '.__('(Note: To specify the Category of the post for which the End timer get displayed.)','voting-contest'));
						?>
					</ul>
				</li>
				<li><b><u>addcontestants</u></b><br/><p><i><?php _e('Attributes that can be passed to this shortcode are as follows:','voting-contest'); ?></i></p>
					 <ul>
						<?php
						echo html('li', '<u>id</u> &nbsp '.__('(Note: To specify the Category of the post for which the contestants to be added.)','voting-contest'));
						echo html('li', '<u>showcontestants</u> &nbsp '.__('(Note: To specify Whether the Contestants get displayed or not.)','voting-contest'));
						?>
					</ul>
				</li>
                <li><b><u>profilescreen</u></b><br/><p><i><?php _e('Attributes that can be passed to this shortcode are as follows:','voting-contest'); ?></i></p>
					 <ul>
						<?php
						echo html('li', '<u>contests</u> &nbsp '.__('(Note: To specify Whether the Contestants get displayed or not.)','voting-contest'));
						echo html('li', '<u>postperpage</u> &nbsp '.__('(Note: To specify the Number of Contestants displayed per page in the Contest Listing)','voting-contest'));
                        echo html('li', '<u>form</u> &nbsp '.__('(Note: To specify Whether the Profile Edit form get displayed or not.)','voting-contest'));
						?>
					</ul>
				</li>
		</ul>
                
               

                
            </div>
        </div>