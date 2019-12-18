<? $entry_info = $information_array[$page_temp];

$retrieve_page->execute(["page_id"=>$page_temp]);
$result = $retrieve_page->fetchAll();
foreach ($result as $row):
	$entry_info['summary'] = json_decode($row['summary'], true);
	$entry_info['body'] = json_decode($row['body'], true);
	$entry_info['studies'] = $row['studies'];
	endforeach;


if (!(empty($messenger_bot)) && file_exists("messenger/".$entry_info['entry_id'].".png")):
	echo "<div id='messenger-code-image' ". $layout_nodisplay_temp .">";
	echo "<a href='http://m.me/".$messenger_bot."?ref=entry_id=".$page_temp."' target='_blank'><amp-img src='/messenger/".$entry_info['entry_id'].".png' width='200px' height='200px'></amp-img></a></div>";
	echo "<a href='/".$page_temp."/flyer/' target='_blank'><div id='messenger-flyer-button' ". $layout_nodisplay_temp .">Get flyer</div></a>";
	endif;

if (!(empty($login))):
	echo "<a href='/".$page_temp."/edit/' target='_blank'><span class='navigation-header-item-option'>&#10033; Edit article</span></a>";
	endif;

echo "<article><div vocab='http://schema.org/' typeof='Article'>";

echo "<header><h1 property='name'><span>" . implode("</span> &bull; <span>", $entry_info['name']) . "</span></h1></header>";

echo "<div class='genealogy_interstice' amp-fx='parallax' data-parallax-factor='1.1'>";

echo "<p ". $layout_nodisplay_temp ."><b>type</b><span>".$entry_info['type']."</span></p>";

if ($entry_info['type'] == "location"):
	$string_temp = "<b ". $layout_nodisplay_temp .">unit</b><span>{{{".$entry_info['unit_id'][0]."}}}</span>";
	echo body_process($string_temp);
	endif;

if (!(empty($entry_info['appendix']['latitude'])) && !(empty($entry_info['appendix']['longitude']))):
	echo "<p><b>map</b>";
	echo "<a href='https://".$domain."/".$entry_info['entry_id']."/map/' target='_blank'><span>";
	echo substr($entry_info['appendix']['latitude'],0,6).", ".substr($entry_info['appendix']['longitude'],0,6);
	echo "</span></a></p>";
	endif;

if (!(empty($entry_info['parents']['hierarchy']))):
	$entry_info['parents']['hierarchy'] = array_unique($entry_info['parents']['hierarchy']);
	$entry_array = [];
	foreach ($entry_info['parents']['hierarchy'] as $parent_id):
		if ($parent_id == $entry_info['entry_id']): continue; endif;
		$entry_array[] = "{{{".$parent_id."}}}";
		endforeach;
	if (!(empty($entry_array))):
		$entry_array = "<b>parents</b><span>".implode("</span><span>", $entry_array)."</span>";
		echo body_process($entry_array);
		endif;
	endif;

if (!(empty($entry_info['children']['hierarchy']))):
	$entry_info['children']['hierarchy'] = array_unique($entry_info['children']['hierarchy']);
	$entry_array = [];
	foreach ($entry_info['children']['hierarchy'] as $child_id):
		if ($child_id == $entry_info['entry_id']): continue; endif;
		$entry_array[] = "{{{".$child_id."}}}";
		endforeach;
	if (!(empty($entry_array))):
		$entry_array = "<b>subpages</b><span>".implode("</span><span>", $entry_array)."</span>";
		echo body_process($entry_array);
		endif;
	endif;

$languages_temp = [];
if (!(empty($entry_info['summary']))): $languages_temp = array_merge($languages_temp, array_keys($entry_info['summary'])); endif;
if (!(empty($entry_info['body']))): $languages_temp = array_merge($languages_temp, array_keys($entry_info['body'])); endif;
if (!(empty($languages_temp))): $languages_temp = array_unique($languages_temp); endif;
if (count($languages_temp) > 1):
	echo "<p><b>languages</b>";
	foreach($languages_temp as $language_temp): echo "<a href='#".$language_temp."'><span>".$language_temp."</span></a>"; endforeach;
	echo "</p>";
	endif;

echo "</div>";

echo "<span property='articleBody'>";

foreach ($languages_temp as $language_temp):
	echo "<span id='".$language_temp."'></span>";
	if (!(empty($entry_info['summary'][$language_temp]))):
		echo body_process(html_entity_decode(htmlspecialchars_decode($entry_info['summary'][$language_temp]))); endif;
	if (!(empty($entry_info['body'][$language_temp]))):
		echo body_process(html_entity_decode(htmlspecialchars_decode($entry_info['body'][$language_temp]))); endif;
	echo "<hr>";
	endforeach;

if (!(empty($entry_info['studies']))):
	echo "<div class='studies'>" . body_process(html_entity_decode(htmlspecialchars_decode($entry_info['studies']))) . "</div>";
	endif;

if ($entry_info['type'] == "person"):
	// person info an terms
//	$terms_array = get_terms(["person_id"=>$page_temp]);
	if (!(empty($terms_array))):
		echo "<hr><table><thead><tr><th>term</th><th>person</th><th>position</th><th>for</th><th>party</th><th>start</th><th>end</th><th>vote</th></tr></thead><tbody>";
		foreach($terms_array as $term_id => $term_info):
			$information_array = get_entries(["entry_id"=>$term_info]);
			echo "<tr><td><a href='?term_id=$term_id'>$term_id</a></td>";
			if (empty($term_info['person_id'])): echo "<td></td>";
			else: echo "<td><a href='?entry_id=".$term_info['person_id']."'>".$information_array[$term_info['person_id']]['name_english'][0]."</a></td>"; endif;
			echo "<td><a href='?entry_id=".$term_info['person_id']."'>".$information_array[$term_info['position_id']]['name_english'][0]."</a></td>";
			echo "<td><a href='?entry_id=".$term_info['person_id']."'>".$information_array[$term_info['for']]['name_english'][0]."</a></td>";
			echo "<td><a href='?entry_id=".$term_info['person_id']."'>".$information_array[$term_info['party_id']]['name_english'][0]."</a></td>";
			echo "<td><a href='?event_id=".$term_info['start_event']."'>".$information_array[$term_info['start_event']]['name_english'][0]."</a></td>";
			if ($term_info['end_event'] == "active"): echo "<td>active</td>";
			else: echo "<td><a href='?event_id=".$term_info['end_event']."'>".$information_array[$term_info['end_event']]['name_english'][0]."</a></td>"; endif;
			echo "<td>".$term_info['vote']."</a></td></tr>";
			endforeach;
		echo "</tbody></table><hr>";
		endif;
	endif;

echo "</span>";

echo "</div></article>"; ?>
