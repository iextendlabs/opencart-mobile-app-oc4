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

/* admin/view/template/marketplace/modification_list.twig */
class __TwigTemplate_c7981cfac965d40e739b5034b9c347b3 extends Template
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
        yield "<form id=\"form-modification\" method=\"post\" data-oc-toggle=\"ajax\" data-oc-load=\"";
        yield ($context["action"] ?? null);
        yield "\" data-oc-target=\"#modification\">
  <div class=\"table-responsive\">
    <table class=\"table table-bordered table-hover\">
      <thead>
        <tr>
          <th class=\"text-center\" style=\"width: 1px;\"><input type=\"checkbox\" onclick=\"\$('input[name*=\\'selected\\']').prop('checked', \$(this).prop('checked'));\" class=\"form-check-input\"/></th>
          <th>";
        // line 7
        if ((($context["sort"] ?? null) == "name")) {
            // line 8
            yield "              <a href=\"";
            yield ($context["sort_name"] ?? null);
            yield "\" class=\"";
            yield Twig\Extension\CoreExtension::lower($this->env->getCharset(), ($context["order"] ?? null));
            yield "\">";
            yield ($context["column_name"] ?? null);
            yield "</a>
            ";
        } else {
            // line 10
            yield "              <a href=\"";
            yield ($context["sort_name"] ?? null);
            yield "\">";
            yield ($context["column_name"] ?? null);
            yield "</a>
            ";
        }
        // line 11
        yield "</th>
          <th>";
        // line 12
        if ((($context["sort"] ?? null) == "author")) {
            // line 13
            yield "              <a href=\"";
            yield ($context["sort_author"] ?? null);
            yield "\" class=\"";
            yield Twig\Extension\CoreExtension::lower($this->env->getCharset(), ($context["order"] ?? null));
            yield "\">";
            yield ($context["column_author"] ?? null);
            yield "</a>
            ";
        } else {
            // line 15
            yield "              <a href=\"";
            yield ($context["sort_author"] ?? null);
            yield "\">";
            yield ($context["column_author"] ?? null);
            yield "</a>
            ";
        }
        // line 16
        yield "</th>
          <th>";
        // line 17
        if ((($context["sort"] ?? null) == "version")) {
            // line 18
            yield "              <a href=\"";
            yield ($context["sort_version"] ?? null);
            yield "\" class=\"";
            yield Twig\Extension\CoreExtension::lower($this->env->getCharset(), ($context["order"] ?? null));
            yield "\">";
            yield ($context["column_version"] ?? null);
            yield "</a>
            ";
        } else {
            // line 20
            yield "              <a href=\"";
            yield ($context["sort_version"] ?? null);
            yield "\">";
            yield ($context["column_version"] ?? null);
            yield "</a>
            ";
        }
        // line 21
        yield "</th>
          <th>";
        // line 22
        if ((($context["sort"] ?? null) == "date_added")) {
            // line 23
            yield "              <a href=\"";
            yield ($context["sort_date_added"] ?? null);
            yield "\" class=\"";
            yield Twig\Extension\CoreExtension::lower($this->env->getCharset(), ($context["order"] ?? null));
            yield "\">";
            yield ($context["column_date_added"] ?? null);
            yield "</a>
            ";
        } else {
            // line 25
            yield "              <a href=\"";
            yield ($context["sort_date_added"] ?? null);
            yield "\">";
            yield ($context["column_date_added"] ?? null);
            yield "</a>
            ";
        }
        // line 26
        yield "</th>
          <th class=\"text-end\">";
        // line 27
        yield ($context["column_action"] ?? null);
        yield "</th>
        </tr>
      </thead>
      <tbody>
        ";
        // line 31
        if (($context["modifications"] ?? null)) {
            // line 32
            yield "          ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["modifications"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["modification"]) {
                // line 33
                yield "            <tr";
                if ( !CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "status", [], "any", false, false, false, 33)) {
                    yield " class=\"table-active opacity-50\"";
                }
                yield ">
              <td class=\"text-center\"><input type=\"checkbox\" name=\"selected[]\" value=\"";
                // line 34
                yield CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "modification_id", [], "any", false, false, false, 34);
                yield "\" class=\"form-check-input\"/></td>
              <td>";
                // line 35
                yield CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "name", [], "any", false, false, false, 35);
                yield "</td>
              <td>";
                // line 36
                yield CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "author", [], "any", false, false, false, 36);
                yield "</td>
              <td>";
                // line 37
                yield CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "version", [], "any", false, false, false, 37);
                yield "</td>
              <td>";
                // line 38
                yield CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "date_added", [], "any", false, false, false, 38);
                yield "</td>
              <td class=\"text-end text-nowrap\">
                <button type=\"button\" data-bs-toggle=\"modal\" data-bs-target=\"#modal-modification-";
                // line 40
                yield CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "modification_id", [], "any", false, false, false, 40);
                yield "\" class=\"btn btn-info\"><i class=\"fa-solid fa-info-circle\"></i></button>
                ";
                // line 41
                if ( !CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "status", [], "any", false, false, false, 41)) {
                    // line 42
                    yield "                  <button type=\"button\" value=\"";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "enable", [], "any", false, false, false, 42);
                    yield "\" data-bs-toggle=\"tooltip\" title=\"";
                    yield ($context["button_enable"] ?? null);
                    yield "\" class=\"btn btn-success\"><i class=\"fa-solid fa-plus-circle\"></i></button>
                ";
                } else {
                    // line 44
                    yield "                  <button type=\"button\" value=\"";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "disable", [], "any", false, false, false, 44);
                    yield "\" data-bs-toggle=\"tooltip\" title=\"";
                    yield ($context["button_disable"] ?? null);
                    yield "\" class=\"btn btn-danger\"><i class=\"fa-solid fa-minus-circle\"></i></button>
                ";
                }
                // line 45
                yield "</td>
            </tr>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['modification'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 48
            yield "        ";
        } else {
            // line 49
            yield "          <tr>
            <td class=\"text-center\" colspan=\"6\">";
            // line 50
            yield ($context["text_no_results"] ?? null);
            yield "</td>
          </tr>
        ";
        }
        // line 53
        yield "      </tbody>
    </table>
  </div>
  <div class=\"row\">
    <div class=\"col-sm-6 text-start\">";
        // line 57
        yield ($context["pagination"] ?? null);
        yield "</div>
    <div class=\"col-sm-6 text-end\">";
        // line 58
        yield ($context["results"] ?? null);
        yield "</div>
  </div>
</form>
";
        // line 61
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["modifications"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["modification"]) {
            // line 62
            yield "  <div id=\"modal-modification-";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "modification_id", [], "any", false, false, false, 62);
            yield "\" class=\"modal modal-lg\">
    <div class=\"modal-dialog\">
      <div class=\"modal-content\">
        <div class=\"modal-header\">
          <h5 class=\"modal-title\"><i class=\"fa-solid fa-info-circle\"></i> ";
            // line 66
            yield ($context["text_info"] ?? null);
            yield "</h5>
          <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\"></button>
        </div>
        <div class=\"modal-body\">
          <div class=\"mb-3\">
            <label class=\"form-label\">";
            // line 71
            yield ($context["entry_name"] ?? null);
            yield "</label>
            <input type=\"text\" value=\"";
            // line 72
            yield CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "name", [], "any", false, false, false, 72);
            yield "\" class=\"form-control\" readonly/>
          </div>
          <div class=\"mb-3\">
            <label class=\"form-label\">";
            // line 75
            yield ($context["entry_description"] ?? null);
            yield "</label>
            <textarea rows=\"5\" class=\"form-control\" readonly>";
            // line 76
            yield CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "description", [], "any", false, false, false, 76);
            yield "</textarea>
          </div>
          <div class=\"mb-3\">
            <label class=\"form-label\">";
            // line 79
            yield ($context["entry_code"] ?? null);
            yield "</label>
            <input type=\"text\" value=\"";
            // line 80
            yield CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "code", [], "any", false, false, false, 80);
            yield "\" class=\"form-control\" readonly/>
          </div>
          <div class=\"mb-3\">
            <label class=\"form-label\">";
            // line 83
            yield ($context["entry_xml"] ?? null);
            yield "</label>
            <textarea rows=\"25\" class=\"form-control codemirror overflow-scroll\" readonly>";
            // line 84
            yield CoreExtension::getAttribute($this->env, $this->source, $context["modification"], "xml", [], "any", false, false, false, 84);
            yield "</textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['modification'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 91
        yield "<link href=\"view/javascript/codemirror/lib/codemirror.css\" rel=\"stylesheet\"/>
<link href=\"view/javascript/codemirror/theme/monokai.css\" rel=\"stylesheet\"/>
<script type=\"text/javascript\" src=\"view/javascript/codemirror/lib/codemirror.js\"></script>
<script type=\"text/javascript\" src=\"view/javascript/codemirror/mode/xml/xml.js\"></script>
<script type=\"text/javascript\" src=\"view/javascript/codemirror/mode/htmlmixed/htmlmixed.js\"></script>
<script type=\"text/javascript\" src=\"view/javascript/codemirror/addon/edit/matchbrackets.js\"></script>
<script type=\"text/javascript\"><!--
// Initialize codemirrror
var codemirror = CodeMirror.fromTextArea(document.querySelector('.codemirror'), {
    mode: 'text/xml',
    lineNumbers: true,
    lineWrapping: true,
    readOnly: true,
    autofocus: false,
    theme: 'monokai'
});

codemirror.setSize('100%', '600px');
//--></script>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "admin/view/template/marketplace/modification_list.twig";
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
        return array (  311 => 91,  298 => 84,  294 => 83,  288 => 80,  284 => 79,  278 => 76,  274 => 75,  268 => 72,  264 => 71,  256 => 66,  248 => 62,  244 => 61,  238 => 58,  234 => 57,  228 => 53,  222 => 50,  219 => 49,  216 => 48,  208 => 45,  200 => 44,  192 => 42,  190 => 41,  186 => 40,  181 => 38,  177 => 37,  173 => 36,  169 => 35,  165 => 34,  158 => 33,  153 => 32,  151 => 31,  144 => 27,  141 => 26,  133 => 25,  123 => 23,  121 => 22,  118 => 21,  110 => 20,  100 => 18,  98 => 17,  95 => 16,  87 => 15,  77 => 13,  75 => 12,  72 => 11,  64 => 10,  54 => 8,  52 => 7,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<form id=\"form-modification\" method=\"post\" data-oc-toggle=\"ajax\" data-oc-load=\"{{ action }}\" data-oc-target=\"#modification\">
  <div class=\"table-responsive\">
    <table class=\"table table-bordered table-hover\">
      <thead>
        <tr>
          <th class=\"text-center\" style=\"width: 1px;\"><input type=\"checkbox\" onclick=\"\$('input[name*=\\'selected\\']').prop('checked', \$(this).prop('checked'));\" class=\"form-check-input\"/></th>
          <th>{% if sort == 'name' %}
              <a href=\"{{ sort_name }}\" class=\"{{ order|lower }}\">{{ column_name }}</a>
            {% else %}
              <a href=\"{{ sort_name }}\">{{ column_name }}</a>
            {% endif %}</th>
          <th>{% if sort == 'author' %}
              <a href=\"{{ sort_author }}\" class=\"{{ order|lower }}\">{{ column_author }}</a>
            {% else %}
              <a href=\"{{ sort_author }}\">{{ column_author }}</a>
            {% endif %}</th>
          <th>{% if sort == 'version' %}
              <a href=\"{{ sort_version }}\" class=\"{{ order|lower }}\">{{ column_version }}</a>
            {% else %}
              <a href=\"{{ sort_version }}\">{{ column_version }}</a>
            {% endif %}</th>
          <th>{% if sort == 'date_added' %}
              <a href=\"{{ sort_date_added }}\" class=\"{{ order|lower }}\">{{ column_date_added }}</a>
            {% else %}
              <a href=\"{{ sort_date_added }}\">{{ column_date_added }}</a>
            {% endif %}</th>
          <th class=\"text-end\">{{ column_action }}</th>
        </tr>
      </thead>
      <tbody>
        {% if modifications %}
          {% for modification in modifications %}
            <tr{% if not modification.status %} class=\"table-active opacity-50\"{% endif %}>
              <td class=\"text-center\"><input type=\"checkbox\" name=\"selected[]\" value=\"{{ modification.modification_id }}\" class=\"form-check-input\"/></td>
              <td>{{ modification.name }}</td>
              <td>{{ modification.author }}</td>
              <td>{{ modification.version }}</td>
              <td>{{ modification.date_added }}</td>
              <td class=\"text-end text-nowrap\">
                <button type=\"button\" data-bs-toggle=\"modal\" data-bs-target=\"#modal-modification-{{ modification.modification_id }}\" class=\"btn btn-info\"><i class=\"fa-solid fa-info-circle\"></i></button>
                {% if not modification.status %}
                  <button type=\"button\" value=\"{{ modification.enable }}\" data-bs-toggle=\"tooltip\" title=\"{{ button_enable }}\" class=\"btn btn-success\"><i class=\"fa-solid fa-plus-circle\"></i></button>
                {% else %}
                  <button type=\"button\" value=\"{{ modification.disable }}\" data-bs-toggle=\"tooltip\" title=\"{{ button_disable }}\" class=\"btn btn-danger\"><i class=\"fa-solid fa-minus-circle\"></i></button>
                {% endif %}</td>
            </tr>
          {% endfor %}
        {% else %}
          <tr>
            <td class=\"text-center\" colspan=\"6\">{{ text_no_results }}</td>
          </tr>
        {% endif %}
      </tbody>
    </table>
  </div>
  <div class=\"row\">
    <div class=\"col-sm-6 text-start\">{{ pagination }}</div>
    <div class=\"col-sm-6 text-end\">{{ results }}</div>
  </div>
</form>
{% for modification in modifications %}
  <div id=\"modal-modification-{{ modification.modification_id }}\" class=\"modal modal-lg\">
    <div class=\"modal-dialog\">
      <div class=\"modal-content\">
        <div class=\"modal-header\">
          <h5 class=\"modal-title\"><i class=\"fa-solid fa-info-circle\"></i> {{ text_info }}</h5>
          <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\"></button>
        </div>
        <div class=\"modal-body\">
          <div class=\"mb-3\">
            <label class=\"form-label\">{{ entry_name }}</label>
            <input type=\"text\" value=\"{{ modification.name }}\" class=\"form-control\" readonly/>
          </div>
          <div class=\"mb-3\">
            <label class=\"form-label\">{{ entry_description }}</label>
            <textarea rows=\"5\" class=\"form-control\" readonly>{{ modification.description }}</textarea>
          </div>
          <div class=\"mb-3\">
            <label class=\"form-label\">{{ entry_code }}</label>
            <input type=\"text\" value=\"{{ modification.code }}\" class=\"form-control\" readonly/>
          </div>
          <div class=\"mb-3\">
            <label class=\"form-label\">{{ entry_xml }}</label>
            <textarea rows=\"25\" class=\"form-control codemirror overflow-scroll\" readonly>{{ modification.xml }}</textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
{% endfor %}
<link href=\"view/javascript/codemirror/lib/codemirror.css\" rel=\"stylesheet\"/>
<link href=\"view/javascript/codemirror/theme/monokai.css\" rel=\"stylesheet\"/>
<script type=\"text/javascript\" src=\"view/javascript/codemirror/lib/codemirror.js\"></script>
<script type=\"text/javascript\" src=\"view/javascript/codemirror/mode/xml/xml.js\"></script>
<script type=\"text/javascript\" src=\"view/javascript/codemirror/mode/htmlmixed/htmlmixed.js\"></script>
<script type=\"text/javascript\" src=\"view/javascript/codemirror/addon/edit/matchbrackets.js\"></script>
<script type=\"text/javascript\"><!--
// Initialize codemirrror
var codemirror = CodeMirror.fromTextArea(document.querySelector('.codemirror'), {
    mode: 'text/xml',
    lineNumbers: true,
    lineWrapping: true,
    readOnly: true,
    autofocus: false,
    theme: 'monokai'
});

codemirror.setSize('100%', '600px');
//--></script>", "admin/view/template/marketplace/modification_list.twig", "D:\\wamp64\\www\\opencart\\mobile_app\\admin\\view\\template\\marketplace\\modification_list.twig");
    }
}
