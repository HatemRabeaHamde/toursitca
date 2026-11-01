<?php

/* Traveller reviews page — UI strings. Review content lives in the database. */

return [
    'meta_title' => 'Avis des voyageurs — écrits après le voyage, jamais avant',
    'breadcrumb' => [
        'experiences' => 'Expériences',
        'reviews' => 'Avis des voyageurs',
    ],
    'hero' => [
        'eyebrow' => 'Avis des voyageurs',
        'title' => 'Écrits après le voyage, jamais',
        'accent' => 'avant.',
        'lede' => 'Chaque avis vient d’un voyageur qui a fait le voyage — lié à une vraie réservation.',
    ],
    'summary' => [
        'average' => 'Moyenne de toutes les expériences',
        'count' => 'Sur :count avis|Sur :count avis',
        'breakdown' => 'Répartition des notes',
        'stars' => ':stars ★',
        'details' => 'Ce que notent les voyageurs',
        'detail' => [
            'hosts' => 'Hôtes',
            'organisation' => 'Organisation',
            'pickup' => 'Prise en charge & horaires',
            'value' => 'Rapport qualité-prix',
        ],
    ],
    'filters' => [
        'label' => 'Filtrer les avis',
        'interests' => 'Envies',
        'all' => 'Tous',
        'experience' => 'Expérience',
        'all_experiences' => 'Toutes les expériences',
        'host' => 'Hôte',
        'all_hosts' => 'Tous les hôtes',
        'rating' => 'Note',
        'any_rating' => 'Toutes les notes',
        'ratings' => [
            5 => '5 étoiles',
            4 => '4 étoiles et plus',
            3 => '3 étoiles et plus',
        ],
        'photos' => 'Avec photos',
        'apply' => 'Appliquer',
    ],
    'verified_only' => 'Réservations vérifiées uniquement',
    'sort' => [
        'label' => 'Trier :',
        'recent' => 'Les plus récents',
        'highest' => 'Mieux notés',
        'lowest' => 'Moins bien notés',
    ],
    'count' => ':count avis|:count avis',
    'card' => [
        'verified' => 'Réservation vérifiée',
        'travelled' => 'Voyage en :date',
        'reply_from' => 'Réponse de :name',
        'reply' => 'Réponse de l’hôte',
        'stars' => 'Noté :rating sur 5',
        'photo' => 'Photo de :name',
    ],
    'more' => [
        'button' => 'Voir plus d’avis',
        'showing' => ':shown avis sur :total',
    ],
    'empty' => [
        'title' => 'Aucun avis ne correspond à ces filtres.',
        'body' => 'Les avis apparaissent ici une fois le voyage terminé.',
        'reset' => 'Effacer les filtres',
    ],
    'anonymous' => 'Voyageur',
];
