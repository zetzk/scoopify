<?php


/**
 * Its responsible for return select when the options are equals
 *
 * @param mixed $variableValue
 * @param mixed $value
 * @return string
 */
function isSelect(mixed $variableValue, mixed $value): string
{
  return ($variableValue == $value) ? 'selected' : '';
}

/**
 * Its responsible for return select when the options are equals
 *
 * @param array<int, mixed> $array
 * @param mixed $value
 * @return string
 */
function isSelectArray($array, $value): string
{
  return in_array($value, $array) ? "selected" : '';
}



function pagination(int $length)
{
  $current = $_GET["page"] ?? 1;
  $content = "";
  for ($i = 1; $i <= $length; $i++) {
    $active = ($i == $current) ? "active" : "";
    $color = ($i == $current) ? "style='background-color: var(--company-color); border: var(--company-color);'" : "";
    $content .= "<li class='page-item'><a class='page-link {$active}' {$color} href='?page={$i}'>{$i}</a></li>";
  }

  $pagination = "<nav>
    <ul class='pagination pagination-sm'>
        $content
    </ul>
  </nav>";

  return $pagination;
}
