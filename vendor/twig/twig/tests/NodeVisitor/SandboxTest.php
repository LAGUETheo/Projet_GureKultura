<?php

namespace Twig\Tests\NodeVisitor;

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Node\BodyNode;
use Twig\Node\CheckToStringNode;
use Twig\Node\EmptyNode;
use Twig\Node\Expression\Variable\ContextVariable;
use Twig\Node\ModuleNode;
use Twig\Node\PrintNode;
use Twig\NodeTraverser;
use Twig\NodeVisitor\SandboxNodeVisitor;
use Twig\Source;

class SandboxTest extends TestCase
{
    public function testGeneratorExpression()
    {
        $env = new Environment(new ArrayLoader());
        $expr = new ContextVariable('foo', 1);
        $expr->setAttribute('is_generator', true);
        $node = new ModuleNode(new BodyNode([new PrintNode($expr, 1)]), null, new EmptyNode(), new EmptyNode(), new EmptyNode(), new EmptyNode(), new Source('foo', 'foo'));
        $traverser = new NodeTraverser($env, [new SandboxNodeVisitor($env)]);
        $node = $traverser->traverse($node);

        $this->assertNotInstanceOf(CheckToStringNode::class, $node->getNode('body')->getNode(0)->getNode('expr'));
        $this->assertSame("// line 1\nyield from (\$context[\"foo\"] ?? null);\n", $env->compile($node->getNode('body')));
    }
}
