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

/* admin/view/template/marketplace/promotion.twig */
class __TwigTemplate_dff85cbce381600de07e45f6c6b7b3e9 extends Template
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
        if (($context["banner"] ?? null)) {
            // line 2
            yield "  <div class=\"text-center\">";
            yield ($context["banner"] ?? null);
            yield "</div>
  <br/>
";
        }
        // line 5
        if (($context["extensions"] ?? null)) {
            // line 6
            yield "  <fieldset id=\"recommended\">
    <legend>";
            // line 7
            yield ($context["text_recommended"] ?? null);
            yield "</legend>
    <div class=\"table-responsive\">
      <table class=\"table table-bordered table-hover\">
        <thead>
          <tr>
            <th>";
            // line 12
            yield ($context["column_name"] ?? null);
            yield "</th>
            <th class=\"text-end\">";
            // line 13
            yield ($context["column_action"] ?? null);
            yield "</th>
          </tr>
        </thead>
        <tbody>
          ";
            // line 17
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["extensions"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["extension"]) {
                // line 18
                yield "            <tr>
              <td><a href=\"";
                // line 19
                yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "href", [], "any", false, false, false, 19);
                yield "\">";
                yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "name", [], "any", false, false, false, 19);
                yield "</a></td>
              <td class=\"text-end\">
                <div class=\"dropdown\">
                  <div class=\"btn-group\">
                    <button type=\"button\" value=\"";
                // line 23
                yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "download", [], "any", false, false, false, 23);
                yield "\" data-bs-toggle=\"tooltip\" title=\"";
                yield ($context["button_download"] ?? null);
                yield "\" class=\"btn btn-primary\"";
                if ( !CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "download", [], "any", false, false, false, 23)) {
                    yield " disabled";
                }
                yield "><i class=\"fa-solid fa-download\"></i></button>
                    <button type=\"button\" data-bs-toggle=\"dropdown\" class=\"btn btn-outline-dark dropdown-toggle dropdown-toggle-split\"><i class=\"fa-solid fa-caret-down\"></i></button>
                    <div class=\"dropdown-menu dropdown-menu-end\">
                      <a href=\"";
                // line 26
                yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "install", [], "any", false, false, false, 26);
                yield "\" class=\"dropdown-item";
                if ( !CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "install", [], "any", false, false, false, 26)) {
                    yield " disabled";
                }
                yield "\"><i class=\"fa-solid fa-plus-circle fa-fw\"></i> ";
                yield ($context["text_install"] ?? null);
                yield "</a> <a href=\"";
                yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "delete", [], "any", false, false, false, 26);
                yield "\" class=\"dropdown-item";
                if ( !CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "delete", [], "any", false, false, false, 26)) {
                    yield " disabled";
                }
                yield "\"><i class=\"fa-regular fa-trash-can fa-fw\"></i> ";
                yield ($context["text_delete"] ?? null);
                yield "</a>
                    </div>
                  </div>
                </div></td>
            </tr>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['extension'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 32
            yield "        </tbody>
      </table>
    </div>
  </fieldset>
";
        }
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "admin/view/template/marketplace/promotion.twig";
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
        return array (  129 => 32,  103 => 26,  91 => 23,  82 => 19,  79 => 18,  75 => 17,  68 => 13,  64 => 12,  56 => 7,  53 => 6,  51 => 5,  44 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% if banner %}
  <div class=\"text-center\">{{ banner }}</div>
  <br/>
{% endif %}
{% if extensions %}
  <fieldset id=\"recommended\">
    <legend>{{ text_recommended }}</legend>
    <div class=\"table-responsive\">
      <table class=\"table table-bordered table-hover\">
        <thead>
          <tr>
            <th>{{ column_name }}</th>
            <th class=\"text-end\">{{ column_action }}</th>
          </tr>
        </thead>
        <tbody>
          {% for extension in extensions %}
            <tr>
              <td><a href=\"{{ extension.href }}\">{{ extension.name }}</a></td>
              <td class=\"text-end\">
                <div class=\"dropdown\">
                  <div class=\"btn-group\">
                    <button type=\"button\" value=\"{{ extension.download }}\" data-bs-toggle=\"tooltip\" title=\"{{ button_download }}\" class=\"btn btn-primary\"{% if not extension.download %} disabled{% endif %}><i class=\"fa-solid fa-download\"></i></button>
                    <button type=\"button\" data-bs-toggle=\"dropdown\" class=\"btn btn-outline-dark dropdown-toggle dropdown-toggle-split\"><i class=\"fa-solid fa-caret-down\"></i></button>
                    <div class=\"dropdown-menu dropdown-menu-end\">
                      <a href=\"{{ extension.install }}\" class=\"dropdown-item{% if not extension.install %} disabled{% endif %}\"><i class=\"fa-solid fa-plus-circle fa-fw\"></i> {{ text_install }}</a> <a href=\"{{ extension.delete }}\" class=\"dropdown-item{% if not extension.delete %} disabled{% endif %}\"><i class=\"fa-regular fa-trash-can fa-fw\"></i> {{ text_delete }}</a>
                    </div>
                  </div>
                </div></td>
            </tr>
          {% endfor %}
        </tbody>
      </table>
    </div>
  </fieldset>
{% endif %}
", "admin/view/template/marketplace/promotion.twig", "D:\\wamp64\\www\\opencart\\mobile_app\\admin\\view\\template\\marketplace\\promotion.twig");
    }
}
