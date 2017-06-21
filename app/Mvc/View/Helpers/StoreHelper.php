<?php
/**
 * Created by PhpStorm.
 * User: Johnny
 * Date: 4/28/14
 * Time: 11:09 AM
 */

class StoreHelper extends Helper {

	public function formatStoreItem(Article $art, $class = 'col-md-4') {
		?>
		<div class="<?=$class?>">
			<div class="store-item">
				<a href="<?=Config::SITE_PATH?>/store/item/<?=$art->getID();?>">
					<div class="name"><?=$art->name;?></div>
					<div class="image">
						<?php if ($art->getPercent() == 0 && $art->getPrice() > 0):?>
						<div class="price pull-right">
							<?php if ($art->isPromotion() && $art->getNewPrice() != 0):?>
								<div style="margin-top: -7px;">
									<span style="text-decoration: line-through;color:yellow;text-shadow: none;padding-left: 8px;font-size: 12px; float:left;">&euro; <?=number_format($art->getPrice(), 2, ',', ' ');?></span><br />
									&euro; <?=number_format($art->getNewPrice(), 2, ',', ' ');?>
								</div>

							<?php else:?>
								&euro; <?=number_format($art->getPrice(), 2, ',', ' ');?>
							<?php endif;?>
						</div>
						<?php elseif ($art->getPercent() > 0):?>
							<div class="price-pct">
								-<?=$art->getPercent();?>%
							</div>
						<?php endif;?>
						<img src="<?=$art->getPrimaryImgUrl();?>" alt="<?=strip_tags($art->description);?>" /><br />

						<div class="item-overlay">
							<div class="fb-share-button" data-href="<?=Config::SITE_PATH?>/store/item/<?=$art->getID()?>" data-type="button"></div>
						</div>
					</div>
				</a>
			</div>
		</div>
		<?php
	}

	public function formatStoreItemIndividual(Article $art) {
		?>
		<div class="store-item">
			<a href="#">
				<div class="name"><?=$art->name;?></div>
				<div class="image">
					<img class="img-responsive" src="<?=$art->getPrimaryImgUrl();?>" alt="<?=strip_tags($art->description);?>" />
					<div class="info">
						<?=nl2br($art->description_short);?>
					</div>
				</div>
			</a>
		</div>
	<?php
	}

	public function formatStoreItemBackend(Article $art, $class = 'col-md-4', $type) {
		?>
		<div class="<?=$class?>">
			<div class="store-item">
				<a href="<?=Config::SITE_PATH?>/backend/articles/edit/<?=$art->getID();?>">
					<div class="name" style="position: relative;">
						<?=$art->name;?>
						<a
								style="position: absolute; right: 5px;top:10px;font-size: 20px;color:red;"
								href="/backend/articles/delete/<?=$art->getID()?>/<?=$type?>" title="Verwijder dit artikel"
								onclick="if(!confirm('Doorgaan met verwijderen?')) return false;">
							<span class="glyphicon glyphicon-remove"></span>
						</a>
					</div>
					<div class="image">
						<img src="<?=$art->getPrimaryImgUrl();?>" alt="<?=strip_tags($art->description);?>" />
						<div class="info">
							<?=nl2br($art->description_short);?>
						</div>
					</div>
				</a>
			</div>
		</div>
	<?php
	}
} 