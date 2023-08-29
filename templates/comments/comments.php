<?php
$good = $neutrally = $bad = 0;
$countComments = count($comments['comments']);

foreach ($comments['rate'] as $rate) {
    if ($rate->rate > 3) {
        $good += 1;
    } elseif ($rate->rate == 3 || $rate->rate == 0) {
        $neutrally += 1;
    } else {
        $bad += 1;
    }
}
$itemsComments = $comments['comments'];
?>
<?php $modulePosition = floor($countComments / 2); ?>
<?php if (count($itemsComments) > 3): ?>
    <a href="#wrapper" class="scomments-form-toogle scomments-add">✎... Добавить отзыв</a>
<?php endif; ?>
<?php if (!empty($itemsComments)): ?>
    <div class="scomments">
        <div class="pagination" style="border-bottom: 1px solid #d6dadd;">
            <?php if ($pagination->countPages > 1): ?>
                <?= $pagination; ?>
            <?php endif; ?>
        </div>
        <div class="scomments-items" itemscope itemtype="https://schema.org/Review">
            <meta itemprop="itemReviewed" itemscope itemtype="https://schema.org/Article">
            <meta itemprop="itemReviewed" content="<?php echo $title; ?>">


            <?php
            //(показ только плохих или только хороших комментов + JS
            $procentGood = ($good * 100) / ($good + $neutrally + $bad);
            $procentNeutrally = ($neutrally * 100) / ($good + $neutrally + $bad);
            $procentBad = ($bad * 100) / ($good + $neutrally + $bad);

            if (!empty($good) || !empty($neutrally) || !empty($bad)): ?>
                <div class="checked_comm_div" id="type_comments">
                    <label class="checked_comm">
                        <input type="radio" value="all" id="type_all" autocomplete="off" name="radio" checked="checked">
                        <span class="span_all">Все отзывы</span>
                        <span id="count_all">
                    <?php echo $good + $neutrally + $bad; ?>
                </span>
                    </label>
                    <label class="checked_comm">
                        <input type="radio" value="good" id="type_good" name="radio" autocomplete="off">
                        <span class="good_all">Положительные</span>
                        <span id="count_good"><?php echo $good; ?> (<?php echo round($procentGood, 1) . '%'; ?>)</span>
                    </label>
                    <label class="checked_comm">
                        <input type="radio" value="neutrally" id="type_neutrally" name="radio" autocomplete="off">
                        <span class="neutrally_all">Нейтральные</span>
                        <span id="count_neutrally"><?php echo $neutrally; ?> (<?php echo round($procentNeutrally, 1) . '%'; ?>)</span>
                    </label>
                    <label class="checked_comm">
                        <input type="radio" value="bad" id="type_bad" name="radio" autocomplete="off">
                        <span class="bad_all">Отрицательные</span>
                        <span id="count_bad"><?php echo $bad; ?> (<?php echo round($procentBad, 1) . '%'; ?>)</span>
                    </label>
                </div>
            <?php endif; ?>
            <?php $i = 0; ?>
            <?php $temp = null; ?>
            <div class="scomments-all">
                <?php foreach ($itemsComments as $item): ?>
                    <?php
                    //вырезаем маты при выводе комента
                    $item->description = preg_replace('~(6лядь|тварь|тварина|6лять|b3ъeб|cock|cunt|e6aль|ebal|eblan|eбaл|eбaть|eбyч|eбать|eбёт|eблантий|fuck|fucker|fucking|xyёв|xyй|xyя|xуе|xуй|xую|zaeb|zaebal|zaebali|zaebat|архипиздрит|ахуел|ахуеть|бздение|бздеть|бздех|бздецы|бздит|бздицы|бздло|бзднуть|бздун|бздунья|бздюха|бздюшка|бздюшко|блябу|блябуду|бляд|бляди|блядина|блядище|блядки|блядовать|блядство|блядун|блядуны|блядунья|блядь|блядюга| блять|вафел|вафлёр|взъебка|взьебка|взьебывать|въеб|въебался|въебенн|въебусь|въебывать|выблядок|выблядыш|выеб|выебать|выебен|выебнулся|выебон|выебываться|выпердеть|высраться|выссаться|вьебен|гавно|гавнюк|гавнючка|гамно|гандон|гнид|гнида|гниды|говенка|говенный|говешка|говназия|говнецо|говнище|говно|говноед|говнолинк|говночист|говнюк|говнюха|говнядина|говняк|говняный|говнять|гондон|доебываться|долбоеб|долбоёб|долбоящер|дрисня|дрист|дристануть|дристать|дристун|дристуха|дрочелло|дрочена|дрочила|дрочилка|дрочистый|дрочить|дрочка|дрочун|е6ал|е6ут|ебтвоюмать|ёбтвоюмать|ёбaн|ебaть|ебyч|ебал|ебало|ебальник|ебан|ебанамать|ебанат|ебаная|ёбаная|ебанический|ебанный|ебанныйврот|ебаное|ебануть|ебануться|ёбаную|ебаный|ебанько|ебарь|ебат|ёбат|ебатория|ебать|ебать-копать|ебаться|ебашить|ебёна| ебет|ебёт|ебец|ебик|ебин|ебись|ебическая|ебки|еблан|ебливый|еблище| ебло|еблыст| ебля|ёбн|ебнуть|ебнуться|ебня|ебошить|ебская|ебский|ебун|ебут|ебуч|ебуче|ебучее|ебучий|ебучим|ебущ|ебырь|елда|елдак|елдачить|заговнять|задрачивать|задристать|задрота|зае6|заё6|заеб|заёб|заеба|заебал|заебанец|заебастая|заебастый|заебать|заебаться|заебашить|заебистое|заёбистое|заебистые|заёбистые|заебистый|заёбистый|заебись|заебошить|заебываться|залуп|залупа|залупаться|залупить|залупиться|замудохаться|запиздячить|засерать|засерун|засеря|засирать|засрун|захуячить|заябестая|злоеб|злоебучая|злоебучее|злоебучий|ибанамат|ибонех|изговнять|изговняться|изъебнуться|ипать|ипаться|ипаццо|Какдвапальцаобоссать|курва|курвятник|лошарa|лошара|лошары|лошок|лярва|малафья| манда|мандавошек|мандавошка|мандавошки|мандей|мандень|мандеть|мандища| мандой| манду|мандюк|минет|минетчик|минетчица|мокрощелка|мокрощёлка|мразь|мудak|мудaк|мудаг|мудак|муде|мудель|мудеть|муди|мудил|мудила|мудистый|мудня|мудоеб|мудозвон|мудоклюй|нахер|нахуй|набздел|набздеть|наговнять|надристать|надрочить|наебать|наебет|наебнуть|наебнуться|наебывать|напиздел|напиздели|напиздело|напиздили|насрать|настопиздить|нахрен|нахуйник|неебет|неебёт|невротебучий|невъебенно|нехира|нехрен|Нехуй|нехуйственно|ниибацо|ниипацца|ниипаццо|ниипет|никуя|нихера|нихуя|обдристаться|обосранец|обосрать|обосцать|обосцаться|обсирать|объебос|обьебатьобьебос|однохуйственно|опездал|опизде|опизденивающе|остоебенить|остопиздеть|отмудохать|отпиздить|отпиздячить|отпороть|отъебись|охуевательский|охуевать|охуевающий|охуел|охуенно|охуеньчик|охуеть|охуительно|охуительный|охуяньчик|охуячивать|охуячить|очкун|падла|падонки|падонок|паскуда|педерас|педик |педрик|педрила|педрилло|педрило|педрилы|пездень|пездит|пездишь|пездо|пездят|пердануть|пердеж|пердение|пердеть|пердильник|перднуть|пёрднуть|пердун|пердунец|пердунина|пердунья|пердуха|пердь|переёбок|пернуть|пёрнуть|пи3д|пи3де|пи3ду|пиzдец|пидар|пидарaс|пидарас|пидарасы|пидары|пидор|пидорасы|пидорка|пидорок|пидоры|пидрас|пизда|пиздануть|пиздануться|пиздарваньчик|пиздато|пиздатое|пиздатый|пизденка|пизденыш|пиздёныш|пиздеть|пиздец|пиздит|пиздить|пиздиться|пиздишь|пиздища|пиздище|пиздобол|пиздоболы|пиздобратия|пиздоватая|пиздоватый|пиздолиз|пиздонутые|пиздорванец|пиздорванка|пиздострадатель|пизду|пиздуй|пиздун|пиздунья|пизды|пиздюга|пиздюк|пиздюлина|пиздюля|пиздят|пиздячить|писбшки|писька|писькострадатель|писюн|писюшка|подговнять|подонки|подонок|подъебнуть|подъебнуться|поебать|поебень|поёбываает|поскуда|посрать|потаскуха|потаскушка|похер|похерил|похерила|похерили|похеру|похрен|похрену|похуй|похуист|похуистка|похую|придурок|приебаться|припиздень|припизднутый|припиздюлина|пробзделся|проблядь|проеб|проебанка|проебать|промандеть|промудеть|пропизделся|пропиздеть|пропиздячить|раздолбай|разхуячить|разъеб|разъеба|разъебай|разъебать|распиздай|распиздеться|распиздяй|распиздяйство|распроеть|сволота|сволочь|сговнять|секель|серун|сестроеб|сикель|сирать|сирывать|спиздел|спиздеть|спиздил|спиздила|спиздили|спиздит|спиздить|срака|сраку|сраный|сранье|срать|срун|ссака|ссышь|стерва|страхопиздище|сука|суки|суходрочка|сучара|сучий|сучка|сучко|сучонок|сучье|сцание|сцать|сцука|сцуки|сцуконах|сцуль|сцыха|сцышь|съебаться|сыкун|трахае6|трахаеб|трахаёб|трахатель|ублюдок|уебать|уёбища|уебище|уёбище|уебищное|уёбищное|уебк|уебки|уёбки|уебок|уёбок|урюк|усраться|ушлепок|х_у_я_р_а|хyё|хyй|хyйня|хамло| хер |херня|херовато|херовина|херовый|хитровыебанный|хитрожопый|хуeм|хуе|хуё|хуевато|хуёвенький|хуевина|хуево|хуевый|хуёвый|хуек|хуёк|хуел|хуем|хуенч|хуеныш|хуенький|хуеплет|хуеплёт|хуепромышленник|хуерик|хуерыло|хуесос|хуесоска|хуета|хуетень|хуею|хуи|хуй|хуйком|хуйло|хуйня|хуйрик|хуище|хуля| хую|хуюл|хуя|хуяк|хуякать|хуякнуть|хуяра|хуясе|хуячить|целка|чмо|чмошник|чмырь|шалава|шалавой|шараёбиться|шлюха|шлюхой|шлюшка|ябывает|пидарство)~miu', '**цензура**', $item->description);
                    ?>
                    <?php if ($countComments > 10 && $modulePosition == $i): ?>
                        <div class="scomments-item">
                            <div class="comments-content">
                                <div class="scomments-title">
				                    	<span class="scomments-vote">
				                    	</span>
                                    <div>
                                    </div>
                                </div>
                                <div>
                                </div>
                                <?php
                                echo '<div class="scomments-text"> <!-- module-comments --></div>';
                                ?>
                            </div>
                        </div>

                    <?php endif; ?>
                    <?php $temp = $item->created; ?>

                    <?php //(показ только плохих или только хороших комментов + JS
                    if ($item->rate >= 4) {
                        $styleComments = 'good_comm';
                        $text_title = 'Хороший отзыв';
                        $smile = '😀';
                    } elseif ($item->rate == 3 || $item->rate == 0) {
                        $styleComments = 'neutrally_comm';
                        $text_title = 'Нейтральный отзыв';
                        $smile = '😐';
                    } else {
                        $styleComments = 'bad_comm';
                        $text_title = 'Плохой отзыв';
                        $smile = '😡';
                    }
                    ?>

                    <div class="scomments-item <?php echo $styleComments ?>"<?php echo (!empty($item->status)) ? '' : ' style="background-color: #ffebeb;"'; ?>>
                        <?php if (!empty($item->registered)): ?>
                            <div class="comments-avatar-registered"
                                 title="<?php echo $text_title . ' зарегистрированного пользователя' ?>"></div>
                        <?php else: ?>
                            <div class="comments-avatar-guest" title="<?php echo $text_title ?>"></div>
                        <?php endif; ?>
                        <div class="comments-content">
                            <div class="scomments-title">
                                <!--noindex-->
                                <span class="scomments-vote">
						<a rel="nofollow" href="#" title="Согласен!" class="scomments-vote-good"
                           data-id="<?php echo $item->id; ?>"
                           data-value="up">Это правда<?php echo (!empty($item->isgood)) ? '<span>' . $item->isgood . '</span>' : ''; ?></a>
						<a rel="nofollow" href="#" title="Не согласен!" class="scomments-vote-poor"
                           data-id="<?php echo $item->id; ?>"
                           data-value="down">Это ложь<?php echo (!empty($item->ispoor)) ? '<span>' . $item->ispoor . '</span>' : ''; ?></a>
					</span>
                                <!--/noindex-->
                                <div>
                                    <a href="#scomment-<?php echo $item->id; ?>"
                                       name="scomment-<?php echo $item->id; ?>"
                                       id="scomment-<?php echo $item->id; ?>">#<?php echo $item->n; ?><span
                                                class="smile"><?php echo $smile; ?></span></a>
                                    <?php if (!empty($item->user_name)): ?>
                                        <span class="scomments-user-name"
                                              itemprop="author"><?php echo $item->user_name; ?></span>
                                    <?php else: ?>
                                        <span class="scomments-guest-name"
                                              itemprop="author"><?php echo $item->guest_name; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div>
                                <span class="scomments-date" itemprop="datePublished"
                                      content="<?php echo $item->created; ?>"><?php echo $item->created; ?></span>
                                <?php if (!empty($item->country) && $item->country != 'unknown'): ?>
                                    <span class="scomments-marker"></span><span
                                            class="scomments-country">
                                        <?php
                                        if($item->ip == '218959204' && !preg_match('~toloka~', $_SERVER['HTTP_REFERER'])){
                                            $add = ' (источник отзыва карты Яндекса)';
                                        } else {
                                            $add = $item->country;
                                        }
                                        echo $add;
                                        ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="scomments-text" itemprop="reviewBody"><?php echo $item->description; ?></div>

                            <?php if (!empty($item->images)): ?>
                                <a href="#" data-id="<?php echo $item->id; ?>" class="scomments-item-images-toogle">Показать
                                    прикрепленное фото</a>
                                <div class="scomments-item-images"></div>
                            <?php endif; ?>

                            <?php if (!empty($user) && !empty($item->edit) && $user->getId() == $item->user_id): ?>
                                <div class="scomments-button-edit"><a class="scomments-control-edit" data-task="edit"
                                                                      data-object-group="<?php echo $item->object_group; ?>"
                                                                      data-object-id="<?php echo $item->object_id; ?>"
                                                                      data-item-id="<?php echo $item->id; ?>" href="#">/
                                        Редактировать отзыв</a>
                                </div>
                            <?php endif; ?>
                            <!--noindex-->
                            <div class="scomments-button-quote"><a
                                        href="?num=<?php echo $item->n; ?>"
                                        class="scomments-form-toogle scomments-reply">Ответить</a></div>
                            <!--/noindex-->
                            <?php if (!empty($user) && $user->isAdmin()): ?>
                                <div class="scomments-control">
                                    <div class="scomments-control-msg"></div>
                                    <a class="scomments-control-edit" data-task="edit"
                                       data-object-group="<?php echo $item->object_group; ?>"
                                       data-object-id="<?php echo $item->object_id; ?>"
                                       data-item-id="<?php echo $item->id; ?>" href="#"></a>
                                    <a class="scomments-control-delete" data-task="remove"
                                       data-object-group="<?php echo $item->object_group; ?>"
                                       data-object-id="<?php echo $item->object_id; ?>"
                                       data-item-id="<?php echo $item->id; ?>" href="#"></a>
                                    <a class="scomments-control-unpublish" data-task="unpublish"
                                       data-object-group="<?php echo $item->object_group; ?>"
                                       data-object-id="<?php echo $item->object_id; ?>"
                                       data-item-id="<?php echo $item->id; ?>" href="#"></a>
                                    <a class="scomments-control-publish" data-task="publish"
                                       data-object-group="<?php echo $item->object_group; ?>"
                                       data-object-id="<?php echo $item->object_id; ?>"
                                       data-item-id="<?php echo $item->id; ?>" href="#"></a>
                                    <a class="scomments-control-blacklist" data-task="blacklist"
                                       data-object-group="<?php echo $item->object_group; ?>"
                                       data-object-id="<?php echo $item->object_id; ?>"
                                       data-item-id="<?php echo $item->id; ?>" href="#"></a>
                                    <span class="scomments-control-ip"><?php echo $item->ip; ?> / <?php echo '(' . long2ip($item->ip) . ')'; ?></span>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                    <?php $i++; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="pagination" style="border-top: 1px solid #d6dadd;">
            <?php if ($pagination->countPages > 1): ?>
                <?= $pagination; ?>
            <?php endif; ?>
        </div>
    </div>
    <div style="margin-top: 5px;"></div>
    <?php if (count($itemsComments) >= 5) {
        echo '<!-- reklama-over-10 -->';
    }
    ?>
<?php endif; ?>
<div class="scomments-anchor"></div>
<div class="scomments-form" id="#ADD">
    <?php if (!empty($comments['blacklist'])): ?>
        <h3>Администратор заблокировал возможность написания отзывов с этого IP
            - <?php echo $_SERVER['REMOTE_ADDR']; ?></h3>
        <p>Если вы считаете, что это произошло по ошибке - напишите на info@rus-trip.ru и укажите свой ip</p>
    <?php else: ?>
        <?php if (!empty($this->reviews)): ?>
            <h3>Отзывы анонимных пользователей отключены</h3>
            <p><?php echo $this->reviews; ?></p>
        <?php else: ?>

            <header>Добавить отзыв</header>
            <div id="msg"></div>

            <div id="wrapper">
                <form id="myform" method="post">

                    <?php if (empty($this->rate)): ?>
                        <div class="colLeft mob-spike">
                            <label>Проголосуйте</label>
                            <p>Вы еще не голосовали</p>
                        </div>
                        <div class="colRight">
                            <select name="star" class="starSelect">
                                <option value="0">выберите оценку ▼</option>
                                <option value="1">Ужасно</option>
                                <option value="2">Плохо</option>
                                <option value="3">Удовлетворительно</option>
                                <option value="4">Хорошо</option>
                                <option value="5">Отлично</option>
                            </select>
                        </div>
                        <div class="colClear"></div>
                    <?php endif; ?>

                    <?php if (empty($user)): ?>
                        <div class="colLeft">
                            <input type="text" name="username" id="username" placeholder="Ваше имя" value=""
                                   class="field">
                            <input type="text" name="email" id="email" placeholder="Ваш E-mail" value="" class="field">
                        </div>
                        <span class="colRight mob-spike">
                            <ul>
                                <li>
                                    <h4>Вы не авторизованы</h4>
                                    Введите ваши данные для обратной связи
                                </li>
                            </ul>
                        </span>
                        <div class="colClear"></div>
                    <?php endif; ?>
                    <!--noindex-->
                    <ul class="mob-spike">
                        <li>Отзывы с оскорблениями будут удалены!</li>
                        <?php if (!empty($user)): ?>
                            <li>С момента написания отзыва, у вас будет 15 минут, в течение которых вы сможете его
                                отредактировать.
                            </li>
                        <?php endif; ?>
                    </ul>
                    <!--/noindex-->
                    <div class="form-wrapper">
                        <div class="flex-container">
                            <div class="descriptionR">
                                <?php if (!empty($this->item)): ?>
                                    <textarea id="description" name="description" style="width: 99%; height: 283px;"><blockquote><a href="#scomment-<?php echo $this->item->id; ?>">#<?php echo $this->num; ?></a> <?php echo mb_substr($this->item->description, 0, 100, 'UTF-8'); ?></blockquote>&nbsp;</textarea>
                                <?php else: ?>
                                    <textarea id="description" name="description"
                                              style="width: 99%; height: 283px;"></textarea>
                                <?php endif; ?>
                            </div>
                        </div>
                            <div class="quiz-wrapper"></div>
                    </div>
                    <?php if (!empty($user)): ?>
                        <div style="margin: 10px 0;">
                            <?php if (!empty($this->subscribe)): ?>
                                <input type="checkbox" name="subscribe" value="1"
                                       checked="checked"> Вы подписаны на уведомления о новых отзывах
                            <?php else: ?>
                                <input type="checkbox" name="subscribe"
                                       value="1"> Подписаться на уведомления о новых отзывах
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php $attach = md5(uniqid('1')); ?>
                    <input type="hidden" name="option" value="com_comments">
                    <input type="hidden" name="view" value="item">
                    <input type="hidden" name="format" value="json">
                    <input type="hidden" id="task1" name="task" value="create">
                    <input type="hidden" name="item_id" value="">
                    <input type="hidden" name="object_group" value="<?php echo $object_group; ?>">
                    <input type="hidden" name="object_id" value="<?php echo $object_id; ?>">
                    <input type="hidden" name="attach" value="<?php echo $attach ?>">
                </form>
                <div class="colClear"></div>
                <div id="slider">
                    <?php if (!empty($this->images)): ?>
                        <?php foreach ($this->images as $image) { ?>
                            <div class="row-slide">
                                <a href="#" data-id="<?php echo $image->id; ?>" data-attach="<?php echo $attach; ?>"
                                   class="remove-slide"></a>
                                <img src="/images/comments/<?php echo $image->thumb; ?>">
                            </div>
                        <?php } ?>
                    <?php endif; ?>
                </div>
                <label>Если хотите, можете добавить к своему отзыву фото</label>
                <form id="upload" action="/post/comment" method="post" enctype="multipart/form-data">
                    <input type="file" name="file" id="file"> <span id="percent">0%</span>
                    <input type="hidden" name="option" value="com_comments">
                    <input type="hidden" name="view" value="images">
                    <input type="hidden" name="format" value="json">
                    <input type="hidden" id="task2" name="task" value="add">
                    <input type="hidden" name="attach" value="<?php echo $attach; ?>">
                    <input type="hidden" name="item_id" value="">
                </form>
                <div id="loader" style="text-align: center"></div>
                <input type="submit" name="submit" id="submit" value="Опубликовать отзыв">
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>
