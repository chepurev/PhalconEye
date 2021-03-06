{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to lantian.ivan@gmail.com so we can send you a copy immediately.
#}

{% if paginator.total_pages > 1 %}
    {% set startIndex = 1 %}

    {% if paginator.total_pages > 10 %}
        {% if paginator.current > 4 %}
            {% set startIndex = startIndex + paginator.current - 4 %}
        {% endif %}
        {% if paginator.total_pages - paginator.current < 10 %}
            {% set startIndex = paginator.total_pages - 9 %}
        {% endif %}
    {% endif %}

    <div class="pagination">
        <ul>
            {% if paginator.current > 1 %}
                <li><a href="{{ helper('paginatorUrl') }}">{{ 'First' |trans }}</a></li>
                <li><a href="{{ helper('paginatorUrl', paginator.before) }}">&laquo;</a></li>
            {% endif %}

            {% for pageIndex in startIndex..paginator.total_pages %}
                {% if pageIndex is startIndex+10 %}
                    {% break %}
                {% endif %}

                <li {% if pageIndex is paginator.current %}class="active"{% endif %}><a
                       href="{{ helper('paginatorUrl', pageIndex) }}">{{ pageIndex }}</a></li>
            {% endfor %}

            {% if paginator.current < paginator.total_pages %}
                <li><a href="{{ helper('paginatorUrl', paginator.current + 1) }}">&raquo;</a></li>
                <li><a href="{{ helper('paginatorUrl', paginator.last ) }}">{{ 'Last' |trans }}</a></li>
            {% endif %}
        </ul>
    </div>
{% endif %}