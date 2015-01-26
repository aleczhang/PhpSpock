<?php
/**
 * This file is part of PhpSpock.
 *
 * PhpSpock is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpSpock is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with PhpSpock.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2015 Alec Zhang <jun850605@gmail.com>
 *
 **/
/**
 * The when block can also parse mock DSL.
 * @author zhangjun
 * @date 2015-01-26
 */

namespace PhpSpock\SpecificationParser;


class WhenBlockParser extends AbstractParser {

    /**
     * @param $code
     * @return \PhpSpock\Specification\SimpleBlock
     */
    public function parse($code) {

        $code = $this->filtrateCode($code);
        $lines = $this->splitCodeExpressions($code, false);

        $block = new \PhpSpock\Specification\WhenBlock();
        $block->setExpressions($lines);

        return $block;
    }
}