<!-- Banner -->
<section id="banner">
	<div class="banner-content">
		<i class="icon fa-cubes"></i>
		<h2>Welkom op mijn portfolio!</h2>
	<!--	<p>Magna feugiat lorem dolor egestas</p>-->
		<ul class="actions">
			<li><a href="/over-mij" class="button big special">Over mij</a></li>
			<li><a href="#contact" class="button big ">Contact</a></li>
			<li><a href="/blog" class="button big alt ">Blog</a></li>
		</ul>
	</div>
</section>

<!-- One -->
<section id="one" class="wrapper style1">
	<div class="container" id="blogposts_index">
		<div class="row">
			<?php foreach($blog_posts as $blog_post):?>
				<div class="col-md-4">
					<article>
						<div class="blog-post-index">
							<div class="image">
								<img src="<?=$blog_post['image']->url;?>" alt="<?=$blog_post['image']->alt;?>" class="img-responsive" />
							</div>
							<div class="meta">
								<div class="title"><h2><?=$blog_post['title'];?></h2></div>
								<div class="date">
									<i class="icon fa-clock-o"></i> <?=$this->Blog->formatMetaDate($blog_post['date_posted']);?>&nbsp;
									|&nbsp;
									<i class="icon fa-list-alt"></i>
									<a href="/blog/overview/<?=$blog_post['categorie'];?>"><?=$blog_post['categorie'];?></a>
								</div>
								<div class="description">
									<p><?=substr(strip_tags($blog_post['content']), 0, 240)?>...</p>
								</div>
								<div class="actions">
									<a href="/blog/post/<?=$this->Blog->formatBlogUrl($blog_post);?>">Lees meer</a>
								</div>
							</div>
						</div>
					</article>
				</div>
			<?php endforeach;?>
		</div>
	</div>
<!--	<header class="major special">-->
<!--		<h2>Recente blog berichten</h2>-->
<!--	</header>-->
<!--	<div class="inner">-->
<!--		--><?php //if (isset($blog_posts) && is_array($blog_posts)):?>
<!--			--><?php //foreach($blog_posts as $i => $blog_post):?>
<!--				--><?php
//					$class_direction = ($i >= 1) ? 'right' : 'left';
//				?>
<!--				<article class="feature --><?//=$class_direction;?><!--">-->
<!--					<span class="image" ><img src="--><?//=$blog_post['image']->url;?><!--" alt="" /></span>-->
<!--					<div class="content">-->
<!--						<h2>--><?//=$blog_post['title'];?><!--</h2>-->
<!--						<p>--><?//=substr(strip_tags($blog_post['content']), 0, 240)?><!--...</p>-->
<!--						<ul class="actions">-->
<!--							<li>-->
<!--								<a href="/blog/post/--><?//=$this->Blog->formatBlogUrl($blog_post);?><!--" class="button alt">Lees meer</a>-->
<!--							</li>-->
<!--						</ul>-->
<!--					</div>-->
<!--				</article>-->
<!--			--><?php //endforeach;?>
<!--		--><?php //endif;?>

<!--		<article class="feature left">-->
<!--			<span class="image"><img src="/img/pic01.jpg" alt="" /></span>-->
<!--			<div class="content">-->
<!--				<h2>Integer vitae libero acrisus egestas placerat  sollicitudin</h2>-->
<!--				<p>Sed egestas, ante et vulputate volutpat, eros pede semper est, vitae luctus metus libero eu augue. Morbi purus libero, faucibus adipiscing, commodo quis, gravida id, est.</p>-->
<!--				<ul class="actions">-->
<!--					<li>-->
<!--						<a href="#" class="button alt">More</a>-->
<!--					</li>-->
<!--				</ul>-->
<!--			</div>-->
<!--		</article>-->
<!--		<article class="feature right">-->
<!--			<span class="image"><img src="/img/pic02.jpg" alt="" /></span>-->
<!--			<div class="content">-->
<!--				<h2>Integer vitae libero acrisus egestas placerat  sollicitudin</h2>-->
<!--				<p>Sed egestas, ante et vulputate volutpat, eros pede semper est, vitae luctus metus libero eu augue. Morbi purus libero, faucibus adipiscing, commodo quis, gravida id, est.</p>-->
<!--				<ul class="actions">-->
<!--					<li>-->
<!--						<a href="#" class="button alt">More</a>-->
<!--					</li>-->
<!--				</ul>-->
<!--			</div>-->
<!--		</article>-->
<!--	</div>-->
<!--	<div class="clear"></div>-->
</section>
<div class="clear"></div>

<!-- Two -->
<section id="two" class="wrapper special">
	<div class="inner">
		<header class="major narrow">
			<h2>Mijn laatste projecten</h2>
			<p>Ipsum dolor tempus commodo turpis adipiscing Tempor placerat sed amet accumsan</p>
		</header>
		<div class="image-grid">
			<a href="#" class="image"><img src="/img/pic03.jpg" alt="" /></a>
			<a href="#" class="image"><img src="/img/pic04.jpg" alt="" /></a>
			<a href="#" class="image"><img src="/img/pic05.jpg" alt="" /></a>
			<a href="#" class="image"><img src="/img/pic06.jpg" alt="" /></a>
			<a href="#" class="image"><img src="/img/pic07.jpg" alt="" /></a>
			<a href="#" class="image"><img src="/img/pic08.jpg" alt="" /></a>
			<a href="#" class="image"><img src="/img/pic09.jpg" alt="" /></a>
			<a href="#" class="image"><img src="/img/pic10.jpg" alt="" /></a>
		</div>
		<ul class="actions">
			<li><a href="/portfolio/" class="button big alt">Bekijk meer in m'n portfolio</a></li>
		</ul>
	</div>
</section>

<!-- Three -->
<!--<section id="three" class="wrapper style3 special">-->
<!--	<div class="inner">-->
<!--		<header class="major narrow	">-->
<!--			<h2>Magna sed consequat tempus</h2>-->
<!--			<p>Ipsum dolor tempus commodo turpis adipiscing Tempor placerat sed amet accumsan</p>-->
<!--		</header>-->
<!--		<ul class="actions">-->
<!--			<li><a href="#" class="button big alt">Magna feugiat</a></li>-->
<!--		</ul>-->
<!--	</div>-->
<!--</section>-->

<!-- Four -->
<section id="contact" class="wrapper style2 special">
	<div class="inner">
		<header class="major narrow">
			<h2>Contacteer mij</h2>
			<p>Heb je een vraag, aanbod of .. ?</p>
		</header>

		<?php
		$session = new Session();
		$contact_message = $session->read('contact');
		$session->remove('contact');
		?>
		<?php if (isset($contact_message) && is_array($contact_message)):?>
			<div class="alert alert-<?=$contact_message['type'];?>">
				<?=$contact_message['content'];?>
			</div>
		<?php endif;?>

		<form action="/contact" method="POST">
			<div class="container 75%">
				<div class="row uniform 50%">
					<div class="6u 12u$(xsmall)">
						<input name="name" placeholder="Naam" type="text" />
					</div>
					<div class="6u$ 12u$(xsmall)">
						<input name="email" placeholder="E-mail adres" type="email" />
					</div>
					<div class="12u$">
						<textarea name="message" placeholder="Bericht" rows="4"></textarea>
					</div>
				</div>
			</div>
			<ul class="actions">
				<li><input type="submit" class="special" value="Verstuur" /></li>
<!--				<li><input type="reset" class="alt" value="Reset" /></li>-->
			</ul>
		</form>
	</div>
</section>