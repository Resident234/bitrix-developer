<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($arResult);
?>
<?if($USER->IsAuthorized()&&$arResult["IS_OPT_USER"]):?>
	<div class="b-cabinet">

		<div class="b-vacancies b-vacancies_companies">
			<?foreach($arResult["COMPANIES"] as $company):?>
				<div class="b-vacancies__item">

					<?$APPLICATION->IncludeComponent(
						"peppers:peppers_form",
						"personal_companies",
						Array(
							"ID" => $company["ID"],
							"DESCRIPTION_FIELDS" => get_company_form_description(),
							"WRAP_CLASS" => "b-vacancies__item"
						)
					);?>

				</div>
			<?endforeach;?>
			<div class="b-vacancies__item new hidden opened">
				<?$APPLICATION->IncludeComponent(
					"peppers:add_user_company",
					"",
					Array(
						"DESCRIPTION_FIELDS" => get_company_form_description(),
					)
				);?>
			</div>
		</div>


		<p>
			<a class="btn add_company"  href="#">+ Добавить компанию</a>
		</p>


	</div>

	<div class="b-popup only-title" id="success_save">
		<div class="b-popup__title title_center">Данные сохранены!</div>
	</div>
<?else:?>
	<div class="b-cabinet__title">Вы не авторизованы или у Вас нет прав на просмотр этого раздела</div>
<?endif;?>





