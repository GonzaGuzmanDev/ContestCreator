<?php

class ContestSeeder extends Seeder {

    static public function createMetadataFields($contestId, $data) {
        $rData = array();
        function createField($id, $name, $order) {
            EntryMetadataField::create(array(
                'contest_id' => $id,
                'label' => $name,
                'type' => 1,
                'required' => 1,
                'visible' => 1,
                'order' => $order
            ));
            return DB::getPdo()->lastInsertId();
        }
        $rData['Title'] = createField($contestId, 'Title', 0);
        $rData['Client'] = createField($contestId, 'Client', 0);
        $rData['Product'] = createField($contestId, 'Product', 0);
        $rData['Agency'] = createField($contestId, 'Agency', 0);
        $rData['URL'] = createField($contestId, 'URL', 0);
        $rData['Description'] = createField($contestId, 'Description', 0);
        $lastIndex = 0;
        foreach($data as $i => $d) {
            $rData[$d->name] = createField($contestId, $d->name, $i + 6);
            $lastIndex = $i + 6;
        }
        $rData['Media'] = createField($contestId, 'Media', $lastIndex + 1);
        return $rData;
    }

    static public function createMedata($labelList, $entryId, $label, $value) {
        $id = $labelList[$label];
        EntryMetadataValue::create(array(
            'entry_id' => $entryId,
            'entry_metadata_field_id' => $id,
            'value' => $value
        ));
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contest_id = 60;
        $user_id = 29;
        $contests = DB::connection('mysql2')->select('SELECT * FROM contests WHERE id = '.$contest_id);
        foreach($contests as $contest) {
            echo "\nCreamos el contest ".$contest->name."(".$contest->id."):\n ";
            Contest::create(array(
                'id' => $contest->id,
                'code' => $contest->code,
                'name' => $contest->name,
                'user_id' => $user_id,
                'start_at' => $contest->opening,
                'finish_at' => $contest->results,
            ));
            $contest_fields = DB::connection('mysql2')->select('SELECT ced.id, ced.name FROM contest_entry_data ced WHERE ced.contest = '.$contest->id.' ORDER BY ced.id ASC');
            $contest_fields = self::createMetadataFields($contest->id, $contest_fields);
            $contest_categories = DB::connection('mysql2')->select('SELECT * FROM categories WHERE contest = '.$contest->id.' ORDER BY id ASC');
            echo "\n\tCreamos las categorías, subcategorías, entries y su metadata: \n ";
            foreach($contest_categories as $index => $category) {
                echo "\n\t".$category->code.". ".$category->name.":\n ";
                Category::create(array(
                    'id' => $category->id,
                    'name' => $category->code.". ".$category->name,
                    'contest_id' => $contest->id,
                    'order' => $index,
                ));
                $contest_categories_subcategories = DB::connection('mysql2')->select('SELECT * FROM subcategories WHERE cat = '.$category->id.' ORDER BY id ASC');
                foreach($contest_categories_subcategories as $index_sub => $subcategory) {
                    echo "\n\t\t".$subcategory->code.". ".$subcategory->smalltitle.":\n ";
                    Category::create(array(
                        'id' => $subcategory->id + 1000,
                        'name' => $subcategory->code.". ".$subcategory->smalltitle,
                        'contest_id' => $contest->id,
                        'order' => $index_sub,
                        'parent_id' => $category->id,
                        'final' => 1
                    ));
                    $subcategory_entries = DB::connection('mysql2')->select('SELECT * FROM entries WHERE subcategory = '.$subcategory->id.' ORDER BY id ASC');
                    foreach($subcategory_entries as $entry) {
                        echo "\n\t\t\t".$entry->id." - ".$entry->title.":\n ";
                        Entry::create(array(
                            'id' => $entry->id,
                            'contest_id' => $contest->id,
                            'user_id' => $user_id,
                        ));
                        EntryCategory::create(array(
                            'category_id' => $subcategory->id + 1000,
                            'entry_id' => $entry->id
                        ));
                        // fixed width
                        $mask = "\t\t\t\t%20s | %-30s \n";
                        printf($mask, 'LABEL', 'VALUE');
                        printf($mask, 'Title', $entry->title);
                        self::createMedata($contest_fields, $entry->id, 'Title', $entry->title);
                        printf($mask, 'Client', $entry->cliente);
                        self::createMedata($contest_fields, $entry->id, 'Client', $entry->cliente);
                        printf($mask, 'Product', $entry->producto);
                        self::createMedata($contest_fields, $entry->id, 'Product', $entry->producto);
                        printf($mask, 'Agency', $entry->agencia);
                        self::createMedata($contest_fields, $entry->id, 'Agency', $entry->agencia);
                        printf($mask, 'URL', $entry->url);
                        self::createMedata($contest_fields, $entry->id, 'URL', $entry->url);
                        printf($mask, 'Description', $entry->descripcion);
                        self::createMedata($contest_fields, $entry->id, 'Description', $entry->descripcion);
                        $entry_metadatas = DB::connection('mysql2')->select('SELECT ced.name, ed.value FROM entry_data ed LEFT JOIN contest_entry_data ced ON ced.id = ed.type WHERE ed.entry = '.$entry->id.' ORDER BY ed.id ASC');
                        foreach($entry_metadatas as $m_index => $metadata) {
                            printf($mask, $metadata->name, $metadata->value);
                            self::createMedata($contest_fields, $entry->id, $metadata->name, $metadata->value);
                        }
                        printf($mask, 'Media', $entry->descripcion);
                        self::createMedata($contest_fields, $entry->id, 'Media', "");
                    }
                }

            }
        }
    }
}