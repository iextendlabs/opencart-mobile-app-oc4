<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* admin/view/template/extension/module.twig */
class __TwigTemplate_fee0e60949751e492caf0452d97dddad extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield ($context["promotion"] ?? null);
        yield "
<fieldset>
  <legend>";
        // line 3
        yield ($context["heading_title"] ?? null);
        yield "</legend>
  <div class=\"alert alert-info\"><i class=\"fa-solid fa-info-circle\"></i> ";
        // line 4
        yield ($context["text_layout"] ?? null);
        yield "</div>
  <div class=\"table-responsive\">
    <table class=\"table table-bordered table-hover\">
      <thead>
        <tr>
          <th>";
        // line 9
        yield ($context["column_name"] ?? null);
        yield "</th>
          <th class=\"text-end\">";
        // line 10
        yield ($context["column_action"] ?? null);
        yield "</th>
        </tr>
      </thead>
      <tbody>
        ";
        // line 14
        if (($context["extensions"] ?? null)) {
            // line 15
            yield "          ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["extensions"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["extension"]) {
                // line 16
                yield "            <tr";
                if (( !CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "module", [], "any", false, false, false, 16) &&  !CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "status", [], "any", false, false, false, 16))) {
                    yield " class=\"table-active opacity-50\"";
                }
                yield ">
              <td><b>";
                // line 17
                yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "name", [], "any", false, false, false, 17);
                yield "</b></td>
              <td class=\"text-end text-nowrap\">
                ";
                // line 19
                if (CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "installed", [], "any", false, false, false, 19)) {
                    // line 20
                    yield "                  ";
                    if (CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "module", [], "any", false, false, false, 20)) {
                        // line 21
                        yield "                    <a href=\"";
                        yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "edit", [], "any", false, false, false, 21);
                        yield "\" data-bs-toggle=\"tooltip\" title=\"";
                        yield ($context["button_add"] ?? null);
                        yield "\" class=\"btn btn-primary\"><i class=\"fa-solid fa-plus-circle\"></i></a>
                  ";
                    } else {
                        // line 23
                        yield "                    <a href=\"";
                        yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "edit", [], "any", false, false, false, 23);
                        yield "\" data-bs-toggle=\"tooltip\" title=\"";
                        yield ($context["button_edit"] ?? null);
                        yield "\" class=\"btn btn-primary\"><i class=\"fa-solid fa-pencil\"></i></a>
                  ";
                    }
                    // line 25
                    yield "                ";
                } else {
                    // line 26
                    yield "                  <button type=\"button\" class=\"btn btn-primary\" disabled=\"disabled\"><i class=\"fa-solid fa-pencil\"></i></button>
                ";
                }
                // line 28
                yield "                ";
                if ( !CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "installed", [], "any", false, false, false, 28)) {
                    // line 29
                    yield "                  <a href=\"";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "install", [], "any", false, false, false, 29);
                    yield "\" data-bs-toggle=\"tooltip\" title=\"";
                    yield ($context["button_install"] ?? null);
                    yield "\" class=\"btn btn-success\"><i class=\"fa-solid fa-plus-circle\"></i></a>
                ";
                } else {
                    // line 31
                    yield "                  <a href=\"";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "uninstall", [], "any", false, false, false, 31);
                    yield "\" data-bs-toggle=\"tooltip\" title=\"";
                    yield ($context["button_uninstall"] ?? null);
                    yield "\" class=\"btn btn-danger\"><i class=\"fa-solid fa-minus-circle\"></i></a>
                ";
                }
                // line 33
                yield "              </td>
            </tr>
            ";
                // line 35
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "module", [], "any", false, false, false, 35));
                foreach ($context['_seq'] as $context["_key"] => $context["module"]) {
                    // line 36
                    yield "              <tr";
                    if ( !CoreExtension::getAttribute($this->env, $this->source, $context["module"], "status", [], "any", false, false, false, 36)) {
                        yield " class=\"table-active opacity-50\"";
                    }
                    yield ">
                <td>&nbsp;&nbsp;&nbsp;<i class=\"fa-solid fa-folder-open\"></i>&nbsp;&nbsp;&nbsp;";
                    // line 37
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["module"], "name", [], "any", false, false, false, 37);
                    yield "</td>
                <td class=\"text-end text-nowrap\"><a href=\"";
                    // line 38
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["module"], "edit", [], "any", false, false, false, 38);
                    yield "\" data-bs-toggle=\"tooltip\" title=\"";
                    yield ($context["button_edit"] ?? null);
                    yield "\" class=\"btn btn-outline-primary\"><i class=\"fa-solid fa-pencil\"></i></a> <a href=\"";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["module"], "delete", [], "any", false, false, false, 38);
                    yield "\" data-bs-toggle=\"tooltip\" title=\"";
                    yield ($context["button_delete"] ?? null);
                    yield "\" class=\"btn btn-outline-danger\"><i class=\"fa-regular fa-trash-can\"></i></a></td>
              </tr>
            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_key'], $context['module'], $context['_parent']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 41
                yield "          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['extension'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 42
            yield "        ";
        } else {
            // line 43
            yield "          <tr>
            <td class=\"text-center\" colspan=\"2\">";
            // line 44
            yield ($context["text_no_results"] ?? null);
            yield "</td>
          </tr>
        ";
        }
        // line 47
        yield "      </tbody>
    </table>
  </div>
</fieldset>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "admin/view/template/extension/module.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  188 => 47,  182 => 44,  179 => 43,  176 => 42,  170 => 41,  155 => 38,  151 => 37,  144 => 36,  140 => 35,  136 => 33,  128 => 31,  120 => 29,  117 => 28,  113 => 26,  110 => 25,  102 => 23,  94 => 21,  91 => 20,  89 => 19,  84 => 17,  77 => 16,  72 => 15,  70 => 14,  63 => 10,  59 => 9,  51 => 4,  47 => 3,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{{ promotion }}
<fieldset>
  <legend>{{ heading_title }}</legend>
  <div class=\"alert alert-info\"><i class=\"fa-solid fa-info-circle\"></i> {{ text_layout }}</div>
  <div class=\"table-responsive\">
    <table class=\"table table-bordered table-hover\">
      <thead>
        <tr>
          <th>{{ column_name }}</th>
          <th class=\"text-end\">{{ column_action }}</th>
        </tr>
      </thead>
      <tbody>
        {% if extensions %}
          {% for extension in extensions %}
            <tr{% if not extension.module and not extension.status %} class=\"table-active opacity-50\"{% endif %}>
              <td><b>{{ extension.name }}</b></td>
              <td class=\"text-end text-nowrap\">
                {% if extension.installed %}
                  {% if extension.module %}
                    <a href=\"{{ extension.edit }}\" data-bs-toggle=\"tooltip\" title=\"{{ button_add }}\" class=\"btn btn-primary\"><i class=\"fa-solid fa-plus-circle\"></i></a>
                  {% else %}
                    <a href=\"{{ extension.edit }}\" data-bs-toggle=\"tooltip\" title=\"{{ button_edit }}\" class=\"btn btn-primary\"><i class=\"fa-solid fa-pencil\"></i></a>
                  {% endif %}
                {% else %}
                  <button type=\"button\" class=\"btn btn-primary\" disabled=\"disabled\"><i class=\"fa-solid fa-pencil\"></i></button>
                {% endif %}
                {% if not extension.installed %}
                  <a href=\"{{ extension.install }}\" data-bs-toggle=\"tooltip\" title=\"{{ button_install }}\" class=\"btn btn-success\"><i class=\"fa-solid fa-plus-circle\"></i></a>
                {% else %}
                  <a href=\"{{ extension.uninstall }}\" data-bs-toggle=\"tooltip\" title=\"{{ button_uninstall }}\" class=\"btn btn-danger\"><i class=\"fa-solid fa-minus-circle\"></i></a>
                {% endif %}
              </td>
            </tr>
            {% for module in extension.module %}
              <tr{% if not module.status %} class=\"table-active opacity-50\"{% endif %}>
                <td>&nbsp;&nbsp;&nbsp;<i class=\"fa-solid fa-folder-open\"></i>&nbsp;&nbsp;&nbsp;{{ module.name }}</td>
                <td class=\"text-end text-nowrap\"><a href=\"{{ module.edit }}\" data-bs-toggle=\"tooltip\" title=\"{{ button_edit }}\" class=\"btn btn-outline-primary\"><i class=\"fa-solid fa-pencil\"></i></a> <a href=\"{{ module.delete }}\" data-bs-toggle=\"tooltip\" title=\"{{ button_delete }}\" class=\"btn btn-outline-danger\"><i class=\"fa-regular fa-trash-can\"></i></a></td>
              </tr>
            {% endfor %}
          {% endfor %}
        {% else %}
          <tr>
            <td class=\"text-center\" colspan=\"2\">{{ text_no_results }}</td>
          </tr>
        {% endif %}
      </tbody>
    </table>
  </div>
</fieldset>
", "admin/view/template/extension/module.twig", "D:\\wamp64\\www\\opencart\\mobile_app\\admin\\view\\template\\extension\\module.twig");
    }
}
