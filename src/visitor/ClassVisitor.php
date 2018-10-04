<?php

/**
 * ClassVisitor.php – ClassInformationDumper
 *
 * Copyright (C) 2018 Jack Noordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Jack
 *
 */

declare(strict_types=1);

namespace jacknoordhuis\classinformationdumper\visitor;

use jacknoordhuis\classinformationdumper\model\ClassModel;
use jacknoordhuis\classinformationdumper\model\ConstantModel;
use jacknoordhuis\classinformationdumper\model\InterfaceModel;
use jacknoordhuis\classinformationdumper\model\MethodModel;
use jacknoordhuis\classinformationdumper\model\PropertyModel;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ClassVisitor extends NodeVisitorAbstract
{
    /** @var ClassModel[] */
    protected $classes = [];

    /** @var InterfaceModel[] */
    protected $interfaces = [];

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            if ($node->name !== null) {
                foreach ($node->stmts as $stmt) {
                    if ($stmt instanceof Node\Stmt\Class_) {
                        $this->classes[] = $this->createClassModel($node->name->toString(), $stmt);
                    } elseif ($stmt instanceof Node\Stmt\Interface_) {
                        $this->interfaces[] = $this->createInterfaceModel($node->name->toString(), $stmt);
                    }
                }
            }
        }
    }

    protected function createClassModel(string $namespace, Node\Stmt\Class_ $stmt): ?ClassModel
    {
        if ($stmt->name instanceof Node\Identifier) {
            $class = new ClassModel($namespace, $stmt->name->name, ClassModel::buildFlags($stmt->isAbstract(), $stmt->isFinal(), $stmt->isAnonymous()));

            foreach ($stmt->stmts as $child) {
                if ($child instanceof Node\Stmt\ClassConst) {
                    $class->addConstant($this->createConstantModel($child));
                } elseif ($child instanceof Node\Stmt\Property) {
                    $class->addProperty($this->createPropertyModel($child));
                } elseif ($child instanceof Node\Stmt\ClassMethod) {
                    $class->addMethod($this->createMethodModel($child));
                }
            }

            return $class;
        }

        return null;
    }

    protected function createInterfaceModel(string $namespace, Node\Stmt\Interface_ $stmt): ?InterfaceModel
    {
        if ($stmt->name instanceof Node\Identifier) {
            $interface = new InterfaceModel($namespace, $stmt->name->name);

            foreach ($stmt->stmts as $child) {
                if ($child instanceof Node\Stmt\ClassConst) {
                    $interface->addConstant($this->createConstantModel($child));
                } elseif ($child instanceof Node\Stmt\ClassMethod) {
                    $interface->addMethod($this->createMethodModel($child));
                }
            }

            return $interface;
        }

        return null;
    }

    protected function createConstantModel(Node\Stmt\ClassConst $stmt): ?ConstantModel
    {
        if (isset($stmt->consts[0])) {
            $const = $stmt->consts[0];
            if ($const->name instanceof Node\Identifier) {
                return new ConstantModel($const->name->name, 0, $this->fetchExprValue($const->value));
            }
        }

        return null;
    }

    protected function createPropertyModel(Node\Stmt\Property $stmt): ?PropertyModel
    {
        if (isset($stmt->props[0])) {
            $property = $stmt->props[0];
            if ($property->name instanceof Node\Identifier) {
                return new PropertyModel($property->name->name, PropertyModel::buildFlags($stmt->isPublic(), $stmt->isProtected(), $stmt->isPrivate(), $stmt->isStatic()), $this->fetchExprValue($property->default));
            }
        }

        return null;
    }

    public function createMethodModel(Node\Stmt\ClassMethod $stmt): ?MethodModel
    {
        if ($stmt->name instanceof Node\Identifier) {
            return new MethodModel($stmt->name->name, MethodModel::buildFlags($stmt->isPublic(), $stmt->isProtected(), $stmt->isPrivate(), $stmt->isStatic(), $stmt->isAbstract(), $stmt->isAbstract(), $stmt->isMagic()));
        }

        return null;
    }

    /**
     * @param null|\PhpParser\Node\Expr $expr
     *
     * @return mixed
     */
    protected function fetchExprValue(?Node\Expr $expr)
    {
        if ($expr instanceof Node\Scalar\DNumber or $expr instanceof Node\Scalar\EncapsedStringPart or $expr instanceof Node\Scalar\LNumber or $expr instanceof Node\Scalar\String_) {
            return $expr->value;
        } elseif ($expr instanceof Node\Expr\Array_) {
            return $this->fetchArrayExprValue($expr);
        } elseif ($expr instanceof Node\Expr\ConstFetch) {
            return $this->fetchConstExprValue($expr);
        } elseif ($expr instanceof Node\Expr\ClassConstFetch) {
            return $this->fetchClassConstExprValue($expr);
        } elseif ($expr instanceof Node\Expr\BinaryOp\BitwiseOr) {
            return $this->fetchBitwiseOrValue($expr);
        }

        return null;
    }

    protected function fetchArrayExprValue(Node\Expr\Array_ $array): array
    {
        $items = [];

        foreach ($array->items as $item) {
            $key = $this->fetchExprValue($item->key);
            if($key === null or $key === '') {
                $items[] = $this->fetchExprValue($item->value);
            } else {
                $items[$key] = $this->fetchExprValue($item->value);
            }
        }

        return $items;
    }

    /**
     * @param \PhpParser\Node\Expr\ConstFetch $const
     *
     * @return mixed
     */
    protected function fetchConstExprValue(Node\Expr\ConstFetch $const)
    {
        if ($const->name instanceof Node\Name) {
            if (isset($const->name->parts[0])) {
                if ($const->name->parts[0] === 'null') {
                    return null;
                }

                return $const->name->parts[0];
            }
        }

        return null;
    }

    protected function fetchClassConstExprValue(Node\Expr\ClassConstFetch $const): ?string
    {
        if ($const->class instanceof Node\Name and $const->name instanceof Node\Identifier) {
            if (isset($const->class->parts[0])) {
                return $const->class->parts[0].'::'.$const->name->name;
            }
        }

        return null;
    }

    protected function fetchBitwiseOrValue(Node\Expr\BinaryOp\BitwiseOr $bitwiseOr): ?string
    {
        if ($bitwiseOr->left instanceof Node\Expr and $bitwiseOr->right instanceof Node\Expr) {
            return $this->fetchExprValue($bitwiseOr->left).$bitwiseOr->getOperatorSigil().$this->fetchExprValue($bitwiseOr->right);
        }

        return null;
    }

    /**
     * Retrieve the classes.
     *
     * @return ClassModel[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * Retrieve the interfaces.
     *
     * @return InterfaceModel[]
     */
    public function getInterfaces(): array
    {
        return $this->interfaces;
    }
}