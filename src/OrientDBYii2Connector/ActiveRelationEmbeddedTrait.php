<?php
namespace OrientDBYii2Connector;

trait ActiveRelationEmbeddedTrait
{
    public $multiple;
    public $primaryModel;
    public $link;
    public $embedded;

    /**
     * @param $name
     * @param $primaryModels
     * @return array
     */
    public function populateRelation($name, &$primaryModels)
    {
        // if (!is_array($this->link)) {
        // throw new InvalidConfigException('Invalid link: it must be an array of key-value pairs.');
        // }

        // viaTable not need in this database
        // $this->filterByModels($primaryModels);

        if (!$this->multiple && count($primaryModels) === 1) {
            foreach ($primaryModels as $i => $primaryModel) {

                if ($primaryModel instanceof ActiveRecordInterface) {
                    $model = $this->one();
                    if(!$this->embedded)
                        $primaryModel->populateRelation($name, $model);
                } else {
                    $rows = $primaryModels[$i][$name];
                    if(empty($rows))
                        return [];
                    $model = $this->populate([$rows]);

                    $primaryModels[$i][$name] = reset($model) ?: $this->one();
                    if(!$this->embedded)
                        $primaryModels[$i]->populateRelation($name, $model);
                }
                // if ($this->inverseOf !== null) { // ??? for what
                // $this->populateInverseRelation($primaryModels, [$model], $name, $this->inverseOf);
                // }
            }

            return [$model];
        } else {
            $link = $this->link;
            $models = [];

            foreach ($primaryModels as $i => $primaryModel) {
                if ($this->multiple && count($link) === 1 && is_array($rows = $primaryModel[$link])) {
                    $model = $this->populate($rows);
//                    $value = $model ?: $this->all(); //?! use (1)
                    $value = is_array($model) ? $model : $this->all(); //?! or (2)

                    if ($primaryModel instanceof ActiveRecordInterface) { // ??? for what
                        $primaryModel->populateRelation($name, $value);
                    } else {
                        $primaryModels[$i][$name] = $value;
                    }
                    array_push($models, $model);
                } else {
                    $rows = $primaryModels[$i][$name];
                    if(empty($rows))
                        return [];

                    $model = $this->populate([$rows]);

                    $primaryModels[$i][$name] = reset($model) ?: $this->one();
                    if(!$this->embedded)
                        $primaryModels[$i]->populateRelation($name, $model);
                }
            }
            // if ($this->inverseOf !== null) {
            // $this->populateInverseRelation($primaryModels, $models, $name, $this->inverseOf);
            // }

            return $models;
        }
    }

    public function via($relationName, callable $callable = null)
    {
        throw new NotSupportedException(__CLASS__ . " orient not need via");
    }

    public function findFor($name, $model)
    {
        throw new NotSupportedException(__CLASS__ . " orient not need junction");
    }
}
