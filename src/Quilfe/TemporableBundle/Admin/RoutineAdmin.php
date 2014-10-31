<?php
/**
 *  Copyright 2014 Vitaly Bormotov <bormvit@mail.ru>
 *
 *  This file is part of Quilfe Temporable.
 *
 *  Quilfe Temporable is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Quilfe Temporable is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Quilfe Temporable. If not, see <http://www.gnu.org/licenses/>.
**/
namespace Quilfe\TemporableBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class RoutineAdmin extends Admin
{
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id', null, ['label' => 'Идентификатор'])
            ->add('name', null, ['label' => 'Текст'])
            ->add('project', null, ['label' => 'Проект'])
            ->add('user', null, ['label' => 'Пользователь'])
            ->add('lastExecuted', null, ['label' => 'Последнее выполнение'])
            ->add('period', null, ['label' => 'Раз в сколько дней выполнять'])
        ;
    }

    /**
     * Конфигурация формы редактирования записи
     * @param  FormMapper $formMapper
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, ['label' => 'Текст'])
            ->add('project', null, ['label' => 'Проект'])
            ->add('user', null, ['label' => 'Пользователь'])
            ->add('lastExecuted', null, ['label' => 'Последнее выполнение'])
            ->add('period', null, ['label' => 'Раз в сколько дней выполнять'])
        ;
    }

    /**
     * Конфигурация списка записей
     *
     * @param  ListMapper $listMapper
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'Идентификатор'])
            ->add('name', null, ['label' => 'Текст'])
            ->add('project', null, ['label' => 'Проект'])
            ->add('user', null, ['label' => 'Пользователь'])
            ->add('lastExecuted', null, ['label' => 'Последнее выполнение'])
            ->add('period', null, ['label' => 'Раз в сколько дней выполнять'])
        ;
    }

     /**
     * Поля, по которым производится поиск в списке записей
     *
     * @param  DatagridMapper $datagridMapper
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'Текст'])
            ->add('project', null, ['label' => 'Проект'])
            ->add('user', null, ['label' => 'Пользователь'])
        ;
    }
}
