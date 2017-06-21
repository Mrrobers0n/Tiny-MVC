<?php

class BlogHelper extends Helper {

	public function formatBlogUrl($post) {
		$id = $post['id'];
		$title = $post['title'];

		$title = str_replace(' ', '-', $title);

//		die(var_dump($post));

		return $id.'-'.$title;
	}

	public function formatBlogPost($post) {
		?>
		<article class="blog-post">
			<div class="date hidden-xs">
				<span class="day"><?=date('d', strtotime($post['date_posted']));?></span>
				<span class="month"><?=$this->_translateMonth(date('m', strtotime($post['date_posted'])));?></span>
			</div>
			<div class="img"></div>
			<div class="content">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3><?=$post['title'];?></h3>
					</div>
					<div class="panel-body">
						<div class="meta">
							Gepost op <?=$this->formatMetaDate($post['date_posted']);?>
						</div>
						<div class="bcontent">
							<?=substr(strip_tags($post['content']), 0, 400);?>...
						</div>
						<div class="buttons">
							<a href="/blog/post/<?=$this->formatBlogUrl($post);?>" class="button small">Lees meer</a>
						</div>
					</div>
				</div>
<!--				<div class="title"><h3>--><?//=$post['title'];?><!--</h3></div>-->

			</div>

			<div class="clear"></div>
		</article>
		<?php
	}

	public function formatMetaDate($date) {
		$timestamp = strtotime($date);
		$month = $this->_translateMonth(date('m', $timestamp));
		$day = $this->_translateDay(date('D', $timestamp));

		return ucfirst($day).', '.date('d', $timestamp).' '.$month.' '.date('Y',$timestamp);
	}

	private function _translateMonth($month) {
		$translations = array(
			'01' => 'Jan',
			'02' => 'Feb',
			'03' => 'Maa',
			'04' => 'Apr',
			'05' => 'Mei',
			'06' => 'Jun',
			'07' => 'Jul',
			'08' => 'Aug',
			'09' => 'Sep',
			'10' => 'Okt',
			'11' => 'Nov',
			'12' => 'Dec',
		);

		return $translations[$month];
	}

	private function _translateDay($day) {
		$translations = array(
			'Mon' => 'ma',
			'Tue' => 'di',
			'Wed' => 'Wo',
			'Thu' => 'Do',
			'Fri' => 'Vr',
			'Sat' => 'Za',
			'Sun' => 'Zo'
		);

		return $translations[$day];
	}
}