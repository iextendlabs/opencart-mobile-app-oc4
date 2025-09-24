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

/* admin/view/template/marketplace/installer_extension.twig */
class __TwigTemplate_56c1881004ed81e67b7edfbfc9f16a77 extends Template
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
        yield "<div class=\"table-responsive\">
  <table class=\"table table-bordered table-hover\">
    <thead>
      <tr>
        <th><a href=\"";
        // line 5
        yield ($context["sort_name"] ?? null);
        yield "\"";
        if ((($context["sort"] ?? null) == "name")) {
            yield " class=\"";
            yield Twig\Extension\CoreExtension::lower($this->env->getCharset(), ($context["order"] ?? null));
            yield "\"";
        }
        yield ">";
        yield ($context["column_name"] ?? null);
        yield "</a></th>
        <th><a href=\"";
        // line 6
        yield ($context["sort_version"] ?? null);
        yield "\"";
        if ((($context["sort"] ?? null) == "version")) {
            yield " class=\"";
            yield Twig\Extension\CoreExtension::lower($this->env->getCharset(), ($context["order"] ?? null));
            yield "\"";
        }
        yield ">";
        yield ($context["column_version"] ?? null);
        yield "</a></th>
        <th><a href=\"";
        // line 7
        yield ($context["sort_date_added"] ?? null);
        yield "\"";
        if ((($context["sort"] ?? null) == "date_added")) {
            yield " class=\"";
            yield Twig\Extension\CoreExtension::lower($this->env->getCharset(), ($context["order"] ?? null));
            yield "\"";
        }
        yield ">";
        yield ($context["column_date_added"] ?? null);
        yield "</a></th>
        <th class=\"text-end\" style=\"min-width: 105px;\">";
        // line 8
        yield ($context["column_action"] ?? null);
        yield "</th>
      </tr>
    </thead>
    <tbody>
      ";
        // line 12
        if (($context["extensions"] ?? null)) {
            // line 13
            yield "        ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["extensions"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["extension"]) {
                // line 14
                yield "          <tr";
                if ( !CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "status", [], "any", false, false, false, 14)) {
                    yield " class=\"table-active opacity-50\"";
                }
                yield ">
            <td>
              ";
                // line 16
                if (CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "link", [], "any", false, false, false, 16)) {
                    // line 17
                    yield "                <a href=\"";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "link", [], "any", false, false, false, 17);
                    yield "\" target=\"_blank\">";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "name", [], "any", false, false, false, 17);
                    yield "</a>
              ";
                } else {
                    // line 19
                    yield "                ";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "name", [], "any", false, false, false, 19);
                    yield "
              ";
                }
                // line 20
                yield "</td>
            <td class=\"align-text-top\">";
                // line 21
                yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "version", [], "any", false, false, false, 21);
                yield "</td>
            <td class=\"align-text-top\">";
                // line 22
                yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "date_added", [], "any", false, false, false, 22);
                yield "</td>
            <td class=\"text-end align-text-top text-nowrap\">
              <button type=\"button\" data-bs-toggle=\"modal\" data-bs-target=\"#extension-install-";
                // line 24
                yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "extension_install_id", [], "any", false, false, false, 24);
                yield "\" class=\"btn btn-info\"><i class=\"fa-solid fa-info-circle\"></i></button>
              ";
                // line 25
                if ( !CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "status", [], "any", false, false, false, 25)) {
                    // line 26
                    yield "                <a href=\"";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "install", [], "any", false, false, false, 26);
                    yield "\" data-bs-toggle=\"tooltip\" title=\"";
                    yield ($context["button_install"] ?? null);
                    yield "\" class=\"btn btn-success\"><i class=\"fa-solid fa-plus-circle\"></i></a>
                <a href=\"";
                    // line 27
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "delete", [], "any", false, false, false, 27);
                    yield "\" data-bs-toggle=\"tooltip\" title=\"";
                    yield ($context["button_delete"] ?? null);
                    yield "\" class=\"btn btn-danger\"><i class=\"fa-regular fa-trash-can\"></i></a>
              ";
                } else {
                    // line 29
                    yield "                <a href=\"";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "uninstall", [], "any", false, false, false, 29);
                    yield "\" data-bs-toggle=\"tooltip\" title=\"";
                    yield ($context["button_uninstall"] ?? null);
                    yield "\" class=\"btn btn-warning\"><i class=\"fa-solid fa-minus-circle\"></i></a>
                <button type=\"button\" data-bs-toggle=\"tooltip\" title=\"";
                    // line 30
                    yield ($context["button_delete"] ?? null);
                    yield "\" class=\"btn btn-danger\" disabled><i class=\"fa-regular fa-trash-can\"></i></button>
              ";
                }
                // line 31
                yield "</td>
          </tr>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['extension'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 34
            yield "      ";
        } else {
            // line 35
            yield "        <tr>
          <td class=\"text-center\" colspan=\"4\">";
            // line 36
            yield ($context["text_no_results"] ?? null);
            yield "</td>
        </tr>
      ";
        }
        // line 39
        yield "    </tbody>
  </table>
</div>
<div class=\"row\">
  <div class=\"col-sm-6 text-start\">";
        // line 43
        yield ($context["pagination"] ?? null);
        yield "</div>
  <div class=\"col-sm-6 text-end\">";
        // line 44
        yield ($context["results"] ?? null);
        yield "</div>
</div>
";
        // line 46
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["extensions"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["extension"]) {
            // line 47
            yield "  <div id=\"extension-install-";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "extension_install_id", [], "any", false, false, false, 47);
            yield "\" class=\"modal\">
    <div class=\"modal-dialog\">
      <div class=\"modal-content\">
        <div class=\"modal-header\">
          <h5 class=\"modal-title\"><i class=\"fa-solid fa-info-circle\"></i> ";
            // line 51
            yield ($context["text_info"] ?? null);
            yield "</h5>
          <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\"></button>
        </div>
        <div class=\"modal-body\">
          <div class=\"mb-3\">
            <label class=\"form-label\">";
            // line 56
            yield ($context["entry_name"] ?? null);
            yield "</label> <input type=\"text\" value=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "name", [], "any", false, false, false, 56);
            yield "\" class=\"form-control\" readonly/>
          </div>
          <div class=\"mb-3\">
            <label class=\"form-label\">";
            // line 59
            yield ($context["entry_description"] ?? null);
            yield "</label>
            <textarea rows=\"5\" class=\"form-control\" readonly>";
            // line 60
            yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "description", [], "any", false, false, false, 60);
            yield "</textarea>
          </div>
          <div>
            <label class=\"form-label\">";
            // line 63
            yield ($context["entry_code"] ?? null);
            yield "</label> <input type=\"text\" value=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["extension"], "code", [], "any", false, false, false, 63);
            yield "\" class=\"form-control\" readonly/>
          </div>
        </div>
      </div>
    </div>
  </div>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['extension'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "admin/view/template/marketplace/installer_extension.twig";
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
        return array (  239 => 63,  233 => 60,  229 => 59,  221 => 56,  213 => 51,  205 => 47,  201 => 46,  196 => 44,  192 => 43,  186 => 39,  180 => 36,  177 => 35,  174 => 34,  166 => 31,  161 => 30,  154 => 29,  147 => 27,  140 => 26,  138 => 25,  134 => 24,  129 => 22,  125 => 21,  122 => 20,  116 => 19,  108 => 17,  106 => 16,  98 => 14,  93 => 13,  91 => 12,  84 => 8,  72 => 7,  60 => 6,  48 => 5,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<div class=\"table-responsive\">
  <table class=\"table table-bordered table-hover\">
    <thead>
      <tr>
        <th><a href=\"{{ sort_name }}\"{% if sort == 'name' %} class=\"{{ order|lower }}\"{% endif %}>{{ column_name }}</a></th>
        <th><a href=\"{{ sort_version }}\"{% if sort == 'version' %} class=\"{{ order|lower }}\"{% endif %}>{{ column_version }}</a></th>
        <th><a href=\"{{ sort_date_added }}\"{% if sort == 'date_added' %} class=\"{{ order|lower }}\"{% endif %}>{{ column_date_added }}</a></th>
        <th class=\"text-end\" style=\"min-width: 105px;\">{{ column_action }}</th>
      </tr>
    </thead>
    <tbody>
      {% if extensions %}
        {% for extension in extensions %}
          <tr{% if not extension.status %} class=\"table-active opacity-50\"{% endif %}>
            <td>
              {% if extension.link %}
                <a href=\"{{ extension.link }}\" target=\"_blank\">{{ extension.name }}</a>
              {% else %}
                {{ extension.name }}
              {% endif %}</td>
            <td class=\"align-text-top\">{{ extension.version }}</td>
            <td class=\"align-text-top\">{{ extension.date_added }}</td>
            <td class=\"text-end align-text-top text-nowrap\">
              <button type=\"button\" data-bs-toggle=\"modal\" data-bs-target=\"#extension-install-{{ extension.extension_install_id }}\" class=\"btn btn-info\"><i class=\"fa-solid fa-info-circle\"></i></button>
              {% if not extension.status %}
                <a href=\"{{ extension.install }}\" data-bs-toggle=\"tooltip\" title=\"{{ button_install }}\" class=\"btn btn-success\"><i class=\"fa-solid fa-plus-circle\"></i></a>
                <a href=\"{{ extension.delete }}\" data-bs-toggle=\"tooltip\" title=\"{{ button_delete }}\" class=\"btn btn-danger\"><i class=\"fa-regular fa-trash-can\"></i></a>
              {% else %}
                <a href=\"{{ extension.uninstall }}\" data-bs-toggle=\"tooltip\" title=\"{{ button_uninstall }}\" class=\"btn btn-warning\"><i class=\"fa-solid fa-minus-circle\"></i></a>
                <button type=\"button\" data-bs-toggle=\"tooltip\" title=\"{{ button_delete }}\" class=\"btn btn-danger\" disabled><i class=\"fa-regular fa-trash-can\"></i></button>
              {% endif %}</td>
          </tr>
        {% endfor %}
      {% else %}
        <tr>
          <td class=\"text-center\" colspan=\"4\">{{ text_no_results }}</td>
        </tr>
      {% endif %}
    </tbody>
  </table>
</div>
<div class=\"row\">
  <div class=\"col-sm-6 text-start\">{{ pagination }}</div>
  <div class=\"col-sm-6 text-end\">{{ results }}</div>
</div>
{% for extension in extensions %}
  <div id=\"extension-install-{{ extension.extension_install_id }}\" class=\"modal\">
    <div class=\"modal-dialog\">
      <div class=\"modal-content\">
        <div class=\"modal-header\">
          <h5 class=\"modal-title\"><i class=\"fa-solid fa-info-circle\"></i> {{ text_info }}</h5>
          <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\"></button>
        </div>
        <div class=\"modal-body\">
          <div class=\"mb-3\">
            <label class=\"form-label\">{{ entry_name }}</label> <input type=\"text\" value=\"{{ extension.name }}\" class=\"form-control\" readonly/>
          </div>
          <div class=\"mb-3\">
            <label class=\"form-label\">{{ entry_description }}</label>
            <textarea rows=\"5\" class=\"form-control\" readonly>{{ extension.description }}</textarea>
          </div>
          <div>
            <label class=\"form-label\">{{ entry_code }}</label> <input type=\"text\" value=\"{{ extension.code }}\" class=\"form-control\" readonly/>
          </div>
        </div>
      </div>
    </div>
  </div>
{% endfor %}
", "admin/view/template/marketplace/installer_extension.twig", "D:\\wamp64\\www\\opencart\\mobile_app\\admin\\view\\template\\marketplace\\installer_extension.twig");
    }
}
