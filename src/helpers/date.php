<?php

function timestampToFormat(string $timestamp, string $format)
{
    return (new DateTime($timestamp))->format($format);
}

function timestampToDate(string $timestamp)
{
    return (new DateTime($timestamp))->format('Y-m-d');
}

function timestampToDateTimeBR(string $timestamp)
{
    return (new DateTime($timestamp))->format('d/m/Y H:i:s');
}

/**
 * Returns the current date in American format
 * @return string
 */
function nowAmericanDate(): string
{
    return (new DateTime())->format("Y-m-d");
}


/**
 * Returns the current date in Brazilian format
 * @return string
 */
function nowBrazilizanDate(): string
{
    return (new DateTime())->format("d/m/Y");
}

/**
 *  Convert date american to brazilian
 * @param string|null $date
 * @return string
 */
function dateAmericanToBrazilian(?string $date): string
{
    if (!$date)
        return '';
    return (new DateTime($date))->format("d/m/Y");
}

/**
 *  Convert date american to brazilian
 * @param string $date
 * @return ?string
 */
function dateBrazilianToAmerican(string $date): ?string
{
    $dateTime = (DateTime::createFromFormat('d/m/Y', $date));
    if ($dateTime instanceof \DateTime) {
        return $dateTime->format("Y-m-d");
    }
    return null;
}

/**
 * Returns the current time
 * @return string
 */
function hour_instant(): string
{
    return (new DateTime())->format("H:i:s");
}


/**
 * Returns the difference from the current date to the date of the parameter
 * @param DateTime $from the date to compare
 * @return string
 */
function getDiffFrom(DateTime $from): string
{
    $diff = $from->diff((new DateTime()));

    if ($diff->y !== 0) {
        if ($diff->y === 1)
            return "{$diff->y} ano";
        else
            return "{$diff->y} anos";
    } else if ($diff->m !== 0) {
        if ($diff->m === 1)
            return "{$diff->m} mês";
        else
            return "{$diff->m} meses";
    } else if ($diff->d !== 0) {
        if ($diff->d === 1)
            return "{$diff->d} dia";
        else
            return "{$diff->d} dias";
    } else if ($diff->h !== 0) {
        if ($diff->h === 1)
            return "{$diff->h} hora";
        else
            return "{$diff->h} horas";
    } else if ($diff->i !== 0) {
        if ($diff->i === 1)
            return "{$diff->i} minuto";
        else
            return "{$diff->i} minutos";
    } else if ($diff->s !== 0) {
        if ($diff->s === 1)
            return "{$diff->s} segundo";
        else
            return "{$diff->s} segundos";
    }

    return '';
}
