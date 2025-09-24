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

/* admin/view/template/marketplace/installer.twig */
class __TwigTemplate_a46802e2b0fba07a9a41c83ef84a539e extends Template
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
        yield ($context["header"] ?? null);
        yield ($context["column_left"] ?? null);
        yield "
<div id=\"content\">
  <div class=\"page-header\">
    <div class=\"container-fluid\">
      <div class=\"float-end\"><button type=\"button\" id=\"button-upload\" data-bs-toggle=\"tooltip\" title=\"";
        // line 5
        yield ($context["button_upload"] ?? null);
        yield "\" class=\"btn btn-primary\"><i class=\"fa-solid fa-upload\"></i></button></div>
      <h1>";
        // line 6
        yield ($context["heading_title"] ?? null);
        yield "</h1>
      <ol class=\"breadcrumb\">
        ";
        // line 8
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["breadcrumbs"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["breadcrumb"]) {
            // line 9
            yield "          <li class=\"breadcrumb-item\"><a href=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["breadcrumb"], "href", [], "any", false, false, false, 9);
            yield "\">";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["breadcrumb"], "text", [], "any", false, false, false, 9);
            yield "</a></li>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['breadcrumb'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 11
        yield "      </ol>
    </div>
  </div>
  <div class=\"container-fluid\">
    <div class=\"card\">
      <div class=\"card-header\"><i class=\"fa-solid fa-puzzle-piece\"></i> ";
        // line 16
        yield ($context["heading_title"] ?? null);
        yield "</div>
      <div class=\"card-body\">
        <fieldset>
          <legend>";
        // line 19
        yield ($context["text_progress"] ?? null);
        yield "</legend>
          <div class=\"row mb-3\">
            <label class=\"col-sm-2 col-form-label\">";
        // line 21
        yield ($context["entry_progress"] ?? null);
        yield "</label>
            <div class=\"col-sm-10 mt-2\">
              <div class=\"progress\">
                <div id=\"progress-bar\" class=\"progress-bar\" style=\"width: 0%;\"></div>
              </div>
              <div id=\"progress-text\"></div>
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend>";
        // line 31
        yield ($context["text_installed"] ?? null);
        yield "</legend>
          <div id=\"extension\">";
        // line 32
        yield ($context["list"] ?? null);
        yield "</div>
        </fieldset>
      </div>
    </div>
  </div>
</div>
<script type=\"text/javascript\"><!--
\$('#extension').on('click', 'thead a, .pagination a', function(e) {
    e.preventDefault();

    \$('#extension').load(this.href);
});

// Upload
\$('#button-upload').on('click', function() {
    var element = this;

    if (!\$('#button-upload').prop('disabled')) {
        \$('#form-upload').remove();

        \$('body').prepend('<form enctype=\"multipart/form-data\" id=\"form-upload\" style=\"display: none;\"><input type=\"file\" name=\"file\"/></form>');

        \$('#form-upload input[name=\\'file\\']').trigger('click');

        \$('#form-upload input[name=\\'file\\']').on('change', function() {
            if ((this.files[0].size / 1024) > ";
        // line 57
        yield ($context["config_file_max_size"] ?? null);
        yield ") {
                \$(this).val('');

                alert('";
        // line 60
        yield ($context["error_upload_size"] ?? null);
        yield "');
            }

            if (!this.files[0].name.endsWith('.ocmod.zip')) {
                \$(this).val('');

                alert('";
        // line 66
        yield ($context["error_file_type"] ?? null);
        yield "');
            }
        });

        if (typeof timer != 'undefined') {
            clearInterval(timer);
        }

        timer = setInterval(function() {
            if (\$('#form-upload input[name=\\'file\\']').val() !== '') {
                clearInterval(timer);

                \$.ajax({
                    url: 'index.php?route=marketplace/installer.upload&user_token=";
        // line 79
        yield ($context["user_token"] ?? null);
        yield "',
                    type: 'post',
                    data: new FormData(\$('#form-upload')[0]),
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        \$(element).button('loading');
                    },
                    complete: function() {
                        \$(element).button('reset');
                    },
                    success: function(json) {
                        if (json['error']) {
                            \$('#alert').prepend('<div class=\"alert alert-danger alert-dismissible\"><i class=\"fa-solid fa-circle-exclamation\"></i> ' + json['error'] + ' <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>');
                        }

                        if (json['success']) {
                            \$('#alert').prepend('<div class=\"alert alert-success alert-dismissible\"><i class=\"fa-solid fa-check-circle\"></i> ' + json['success'] + ' <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>');

                            \$('#extension').load('index.php?route=marketplace/installer.list&user_token=";
        // line 100
        yield ($context["user_token"] ?? null);
        yield "');
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(thrownError + \"\\r\\n\" + xhr.statusText + \"\\r\\n\" + xhr.responseText);
                    }
                });
            }
        }, 500);
    }
});

// Install
\$('#extension').on('click', '.btn-success, .btn-warning', function(e) {
    e.preventDefault();

    var element = this;

    \$(element).button('loading');

    var next = \$(element).attr('href');

    \$('#progress-bar').removeClass('bg-danger bg-success').css('width', '0%');
    \$('#progress-text').html('');

    var installer = function() {
        return \$.ajax({
            url: next,
            dataType: 'json',
            success: function(json) {
                console.log(json);

                \$('.alert-dismissible').remove();

                if (json['error']) {
                    \$('#progress-bar').addClass('bg-danger');
                    \$('#progress-text').html('<div class=\"text-danger\">' + json['error'] + '</div>');

                    \$(element).button('reset');
                }

                if (json['text']) {
                    \$('#progress-bar').addClass('bg-success');
                    \$('#progress-text').html('<div class=\"text-success\">' + json['text'] + '</div>');
                }

                if (json['success']) {
                    \$('#progress-bar').addClass('bg-success').css('width', '100%');
                    \$('#progress-text').html('<div class=\"text-success\">' + json['success'] + '</div>');

                    \$(element).button('reset');

                    \$('#extension').load('index.php?route=marketplace/installer.list&user_token=";
        // line 152
        yield ($context["user_token"] ?? null);
        yield "');
                }

                if (json['next']) {
                    next = json['next'];

                    chain.attach(installer);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(thrownError + \"\\r\\n\" + xhr.statusText + \"\\r\\n\" + xhr.responseText);

                \$(element).button('reset');
            }
        });
    };

    chain.attach(installer);
});

// Delete
\$('#extension').on('click', '.btn-danger', function(e) {
    e.preventDefault();

    var element = this;

    if (confirm('";
        // line 178
        yield ($context["text_confirm"] ?? null);
        yield "')) {
        \$.ajax({
            url: \$(element).attr('href'),
            dataType: 'json',
            beforeSend: function() {
                \$(element).button('loading');
            },
            complete: function() {
                \$(element).button('reset');
            },
            success: function(json) {
                console.log(json);

                \$('.alert-dismissible').remove();

                if (json['error']) {
                    \$('#alert').prepend('<div class=\"alert alert-danger alert-dismissible\"><i class=\"fa-solid fa-circle-exclamation\"></i> ' + json['error'] + ' <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>');
                }

                if (json['success']) {
                    \$('#alert').prepend('<div class=\"alert alert-success alert-dismissible\"><i class=\"fa-solid fa-check-circle\"></i> ' + json['success'] + ' <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>');

                    \$('#extension').load('index.php?route=marketplace/installer.list&user_token=";
        // line 200
        yield ($context["user_token"] ?? null);
        yield "');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(thrownError + \"\\r\\n\" + xhr.statusText + \"\\r\\n\" + xhr.responseText);
            }
        });
    }
});

\$('.tab-content .btn-danger').on('click', function() {
    var element = this;

    if (confirm('";
        // line 213
        yield ($context["text_confirm"] ?? null);
        yield "')) {
        \$.ajax({
            url: \$(element).attr('value'),
            dataType: 'json',
            beforeSend: function() {
                \$(element).button('loading');
            },
            complete: function() {
                \$(element).button('reset');
            },
            success: function(json) {
                \$('.alert-dismissible').remove();

                if (json['error']) {
                    \$('#alert').prepend('<div class=\"alert alert-danger alert-dismissible\"><i class=\"fa-solid fa-circle-exclamation\"></i> ' + json['error'] + ' <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>');
                }

                if (json['success']) {
                    \$('#alert').prepend('<div class=\"alert alert-success alert-dismissible\"><i class=\"fa-solid fa-check-circle\"></i> ' + json['success'] + ' <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>');

                    \$('#' + \$(element).attr('data-oc-target')).val('');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(thrownError + \"\\r\\n\" + xhr.statusText + \"\\r\\n\" + xhr.responseText);
            }
        });
    }
});
//--></script>
";
        // line 243
        yield ($context["footer"] ?? null);
        yield "
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "admin/view/template/marketplace/installer.twig";
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
        return array (  350 => 243,  317 => 213,  301 => 200,  276 => 178,  247 => 152,  192 => 100,  168 => 79,  152 => 66,  143 => 60,  137 => 57,  109 => 32,  105 => 31,  92 => 21,  87 => 19,  81 => 16,  74 => 11,  63 => 9,  59 => 8,  54 => 6,  50 => 5,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{{ header }}{{ column_left }}
<div id=\"content\">
  <div class=\"page-header\">
    <div class=\"container-fluid\">
      <div class=\"float-end\"><button type=\"button\" id=\"button-upload\" data-bs-toggle=\"tooltip\" title=\"{{ button_upload }}\" class=\"btn btn-primary\"><i class=\"fa-solid fa-upload\"></i></button></div>
      <h1>{{ heading_title }}</h1>
      <ol class=\"breadcrumb\">
        {% for breadcrumb in breadcrumbs %}
          <li class=\"breadcrumb-item\"><a href=\"{{ breadcrumb.href }}\">{{ breadcrumb.text }}</a></li>
        {% endfor %}
      </ol>
    </div>
  </div>
  <div class=\"container-fluid\">
    <div class=\"card\">
      <div class=\"card-header\"><i class=\"fa-solid fa-puzzle-piece\"></i> {{ heading_title }}</div>
      <div class=\"card-body\">
        <fieldset>
          <legend>{{ text_progress }}</legend>
          <div class=\"row mb-3\">
            <label class=\"col-sm-2 col-form-label\">{{ entry_progress }}</label>
            <div class=\"col-sm-10 mt-2\">
              <div class=\"progress\">
                <div id=\"progress-bar\" class=\"progress-bar\" style=\"width: 0%;\"></div>
              </div>
              <div id=\"progress-text\"></div>
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend>{{ text_installed }}</legend>
          <div id=\"extension\">{{ list }}</div>
        </fieldset>
      </div>
    </div>
  </div>
</div>
<script type=\"text/javascript\"><!--
\$('#extension').on('click', 'thead a, .pagination a', function(e) {
    e.preventDefault();

    \$('#extension').load(this.href);
});

// Upload
\$('#button-upload').on('click', function() {
    var element = this;

    if (!\$('#button-upload').prop('disabled')) {
        \$('#form-upload').remove();

        \$('body').prepend('<form enctype=\"multipart/form-data\" id=\"form-upload\" style=\"display: none;\"><input type=\"file\" name=\"file\"/></form>');

        \$('#form-upload input[name=\\'file\\']').trigger('click');

        \$('#form-upload input[name=\\'file\\']').on('change', function() {
            if ((this.files[0].size / 1024) > {{ config_file_max_size }}) {
                \$(this).val('');

                alert('{{ error_upload_size }}');
            }

            if (!this.files[0].name.endsWith('.ocmod.zip')) {
                \$(this).val('');

                alert('{{ error_file_type }}');
            }
        });

        if (typeof timer != 'undefined') {
            clearInterval(timer);
        }

        timer = setInterval(function() {
            if (\$('#form-upload input[name=\\'file\\']').val() !== '') {
                clearInterval(timer);

                \$.ajax({
                    url: 'index.php?route=marketplace/installer.upload&user_token={{ user_token }}',
                    type: 'post',
                    data: new FormData(\$('#form-upload')[0]),
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        \$(element).button('loading');
                    },
                    complete: function() {
                        \$(element).button('reset');
                    },
                    success: function(json) {
                        if (json['error']) {
                            \$('#alert').prepend('<div class=\"alert alert-danger alert-dismissible\"><i class=\"fa-solid fa-circle-exclamation\"></i> ' + json['error'] + ' <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>');
                        }

                        if (json['success']) {
                            \$('#alert').prepend('<div class=\"alert alert-success alert-dismissible\"><i class=\"fa-solid fa-check-circle\"></i> ' + json['success'] + ' <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>');

                            \$('#extension').load('index.php?route=marketplace/installer.list&user_token={{ user_token }}');
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(thrownError + \"\\r\\n\" + xhr.statusText + \"\\r\\n\" + xhr.responseText);
                    }
                });
            }
        }, 500);
    }
});

// Install
\$('#extension').on('click', '.btn-success, .btn-warning', function(e) {
    e.preventDefault();

    var element = this;

    \$(element).button('loading');

    var next = \$(element).attr('href');

    \$('#progress-bar').removeClass('bg-danger bg-success').css('width', '0%');
    \$('#progress-text').html('');

    var installer = function() {
        return \$.ajax({
            url: next,
            dataType: 'json',
            success: function(json) {
                console.log(json);

                \$('.alert-dismissible').remove();

                if (json['error']) {
                    \$('#progress-bar').addClass('bg-danger');
                    \$('#progress-text').html('<div class=\"text-danger\">' + json['error'] + '</div>');

                    \$(element).button('reset');
                }

                if (json['text']) {
                    \$('#progress-bar').addClass('bg-success');
                    \$('#progress-text').html('<div class=\"text-success\">' + json['text'] + '</div>');
                }

                if (json['success']) {
                    \$('#progress-bar').addClass('bg-success').css('width', '100%');
                    \$('#progress-text').html('<div class=\"text-success\">' + json['success'] + '</div>');

                    \$(element).button('reset');

                    \$('#extension').load('index.php?route=marketplace/installer.list&user_token={{ user_token }}');
                }

                if (json['next']) {
                    next = json['next'];

                    chain.attach(installer);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(thrownError + \"\\r\\n\" + xhr.statusText + \"\\r\\n\" + xhr.responseText);

                \$(element).button('reset');
            }
        });
    };

    chain.attach(installer);
});

// Delete
\$('#extension').on('click', '.btn-danger', function(e) {
    e.preventDefault();

    var element = this;

    if (confirm('{{ text_confirm }}')) {
        \$.ajax({
            url: \$(element).attr('href'),
            dataType: 'json',
            beforeSend: function() {
                \$(element).button('loading');
            },
            complete: function() {
                \$(element).button('reset');
            },
            success: function(json) {
                console.log(json);

                \$('.alert-dismissible').remove();

                if (json['error']) {
                    \$('#alert').prepend('<div class=\"alert alert-danger alert-dismissible\"><i class=\"fa-solid fa-circle-exclamation\"></i> ' + json['error'] + ' <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>');
                }

                if (json['success']) {
                    \$('#alert').prepend('<div class=\"alert alert-success alert-dismissible\"><i class=\"fa-solid fa-check-circle\"></i> ' + json['success'] + ' <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>');

                    \$('#extension').load('index.php?route=marketplace/installer.list&user_token={{ user_token }}');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(thrownError + \"\\r\\n\" + xhr.statusText + \"\\r\\n\" + xhr.responseText);
            }
        });
    }
});

\$('.tab-content .btn-danger').on('click', function() {
    var element = this;

    if (confirm('{{ text_confirm }}')) {
        \$.ajax({
            url: \$(element).attr('value'),
            dataType: 'json',
            beforeSend: function() {
                \$(element).button('loading');
            },
            complete: function() {
                \$(element).button('reset');
            },
            success: function(json) {
                \$('.alert-dismissible').remove();

                if (json['error']) {
                    \$('#alert').prepend('<div class=\"alert alert-danger alert-dismissible\"><i class=\"fa-solid fa-circle-exclamation\"></i> ' + json['error'] + ' <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>');
                }

                if (json['success']) {
                    \$('#alert').prepend('<div class=\"alert alert-success alert-dismissible\"><i class=\"fa-solid fa-check-circle\"></i> ' + json['success'] + ' <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>');

                    \$('#' + \$(element).attr('data-oc-target')).val('');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(thrownError + \"\\r\\n\" + xhr.statusText + \"\\r\\n\" + xhr.responseText);
            }
        });
    }
});
//--></script>
{{ footer }}
", "admin/view/template/marketplace/installer.twig", "D:\\wamp64\\www\\opencart\\mobile_app\\admin\\view\\template\\marketplace\\installer.twig");
    }
}
