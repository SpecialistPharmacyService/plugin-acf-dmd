<?php

use makeandship\dmd\DmdClient;
use makeandship\dmd\Util;
use makeandship\logging\Log;

// exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// check if class already exists
if (!class_exists('acf_field_amp')):

    class acf_dmd_field extends acf_field
{

        /*
         * title_case
         *
         * Switch non-title case strings to title case to match underlying
         * content stored in elastic search
         *
         * e.g. CAR to Car
         */
        public function title_case($name)
    {
            if ($name) {
                return ucwords(strtolower($name), " ");
            }
            return $name;
        }

        /*
         *  find_by_contexts
         *
         *  Find all matching documents for a given context and name prefix
         *
         *  @type    function
         *  @date    01/06/2016
         *  @since    1.0.0
         *
         *  @param    $contexts to search for
         *  @param    $name partial string match
         *  @return    $results array
         */
        public function find_by_contexts($contexts, $name)
    {
            $dmd = new DmdClient();

            $query = array(
                '_source' => [
                    'nm',
                    'type',
                    'desc',
                ],
                'suggest' => array(
                    'medicine' => array(
                        'prefix'     => $name,
                        'completion' => array(
                            'field'    => 'nm_suggest',
                            'contexts' => $contexts,
                            "size"     => 50,
                        ),
                    ),
                ),
            );

            Log::debug("acf_dmd_field#find_by_contexts: query: " . json_encode($query));

            return $dmd->search($query);
        }

        /*
         *  find_by_types
         *
         *  Find all matching VTMs to a query against the medicines API
         *
         *  @type    function
         *  @date    01/06/2016
         *  @since    1.0.0
         *
         *  @param    $types to search for
         *  @param    $name partial string match
         *  @return    $results array
         */
        public function find_by_types($types, $name)
    {
            $contexts = array(
                'type' => $types,
            );
            return $this->find_by_contexts($contexts, $name);
        }

        /**
         * Transform a set of API results into id / text pairs
         */
        public function transform($matches)
    {
            $results = Util::safely_get_attribute($matches, 'results');

            $transformed = array(
                "results" => array(),
            );

            foreach ($results as $id => $text) {
                $transformed["results"][] = array(
                    "id"   => strval($id),
                    "text" => $text,
                );
            }

            return $transformed;
        }

        /*
         *  find_vmps
         *
         *  Find all matching VTMs or VMPs to a query against the medicines API
         *
         *  @type    function
         *  @date    01/06/2016
         *  @since    1.0.0
         *
         *  @param    $args post args (s contains the query)
         *  @return    $results array of vtm or vmp -> [ampp] results
         */
        public function get_medicine_by_id($id)
    {
            $dmd      = new DmdClient();
            $document = $dmd->get_document_by_id($id);
            return $document;
        }

        /*
         *  find_vmps
         *
         *  Find all matching VTMs or VMPs to a query against the medicines API
         *
         *  @type    function
         *  @date    01/06/2016
         *  @since    1.0.0
         *
         *  @param    $args post args (s contains the query)
         *  @return    $results array of vtm or vmp -> [ampp] results
         */
        public function get_name_by_id($id)
    {
            $name = null;

            $medicine = $this->get_medicine_by_id($id);

            if ($medicine) {
                $nm   = Util::safely_get_attribute($medicine, 'nm');
                $desc = Util::safely_get_attribute($medicine, 'desc');
                $type = Util::safely_get_attribute($medicine, 'type');

                $name_for_type = $type == 'AMP' ? $desc : $nm;

                $name = $name_for_type . " [" . $type . "]";
            }

            return $name;
        }
    }

// class_exists check
endif;
