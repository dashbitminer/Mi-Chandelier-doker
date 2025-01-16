<?php

namespace App\Decorators\Elearning;

class CoursePreviewDecorator
{
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function scopeName()
    {
        $statusName = '';
        switch ($this->object->scope) {
            case 'country':
                $statusName = 'Nacional';
                break;
            case 'project':
                $statusName = 'Proyectos';
                break;
            case 'all':
                $statusName = 'Regional';
                break;
            default:
                $statusName = '--';
                break;
        }

        return $statusName;
    }

    public function courseTypeName()
    {
        $statusName = '';
        switch ($this->object->course_type) {
            case 'required':
                $statusName = 'Curso obligatorio';
                break;
            case 'optional':
                $statusName = 'Curso opcional';
                break;
            case 'wizard':
                $statusName = 'Ayuda';
                break;
            default:
                $statusName = '--';
                break;
        }

        return $statusName;
    }

    public function statusName()
    {
        $statusName = '';
        switch ($this->object->status) {
            case 'published':
                $statusName = 'Publicado';
                break;
            case 'unpublished':
                $statusName = 'Sin publicar';
                break;
            default:
                $statusName = '--';
                break;
        }

        return $statusName;
    }

    public function active()
    {
        return $this->object->status == 'published';
    }

    public function categoryName()
    {
        return $this->object->category->name;
    }

    public function toArray()
    {
        return [
            'id' => $this->object->id,
            'name' => $this->object->name,
            'description' => $this->object->description,
            'scopeName' => $this->scopeName(),
            'statusName' => $this->statusName(),
            'status' => $this->object->status,
            'active' => $this->active() ? 'true' : 'false',
            'courseTypeName' => $this->courseTypeName(),
            'categoryName' => $this->categoryName(),
        ];
    }
}
