<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    items: Object,
    categories: Object,
});

const showAddModal = ref(false);
const showEditModal = ref(false);

const articleForm = useForm({
    art_id: null,
    art_reference: '',
    art_nom: '',
    art_unite: 'unité',
    art_cat_id: '',
    art_seuil_alerte: 0,
    art_stock_securite: 0,
    art_prix_defaut: 0
});

const categoryForm = useForm({
    cat_id: null,
    cat_code: '',
    cat_nom: '',
    cat_description: ''
});

// --- ARTICLES ---
const addArticle = () => {
    articleForm.post(route('gestionnaire.articles.store'), {
        onSuccess: () => {
            showAddModal.value = false;
            articleForm.reset();
        }
    });
};

const openEditModal = (article) => {
    articleForm.clearErrors();
    articleForm.art_id = article.art_id;
    articleForm.art_reference = article.art_reference;
    articleForm.art_nom = article.art_nom;
    articleForm.art_unite = article.art_unite;
    articleForm.art_cat_id = article.art_cat_id;
    articleForm.art_seuil_alerte = article.art_seuil_alerte;
    articleForm.art_stock_securite = article.art_stock_securite;
    articleForm.art_prix_defaut = article.art_prix_defaut;
    showEditModal.value = true;
};

const updateArticle = () => {
    articleForm.put(route('gestionnaire.articles.update', articleForm.art_id), {
        onSuccess: () => {
            showEditModal.value = false;
            articleForm.reset();
        }
    });
};

const deleteArticle = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
        router.delete(route('gestionnaire.articles.destroy', id));
    }
};

// --- CATÉGORIES ---
const showAddCategoryModal = ref(false);
const showEditCategoryModal = ref(false);

const addCategory = () => {
    categoryForm.post(route('gestionnaire.categories.store'), {
        onSuccess: () => {
            showAddCategoryModal.value = false;
            categoryForm.reset();
        }
    });
};

const openEditCategoryModal = (category) => {
    categoryForm.clearErrors();
    categoryForm.cat_id = category.cat_id;
    categoryForm.cat_code = category.cat_code;
    categoryForm.cat_nom = category.cat_nom;
    categoryForm.cat_description = category.cat_description;
    showEditCategoryModal.value = true;
};

const updateCategory = () => {
    categoryForm.put(route('gestionnaire.categories.update', categoryForm.cat_id), {
        onSuccess: () => {
            showEditCategoryModal.value = false;
            categoryForm.reset();
        }
    });
};

const deleteCategory = (id) => {
    if (confirm('Supprimer cette catégorie ?')) {
        router.delete(route('gestionnaire.categories.destroy', id));
    }
};

const navigation = [
    { name: 'Tableau de bord', route: 'gestionnaire.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Articles', route: 'gestionnaire.articles.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Mouvements', route: 'gestionnaire.mouvements.index', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'Demandes', route: 'gestionnaire.demandes.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Rapports', route: 'gestionnaire.rapports.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Utilisateur', route: 'gestionnaire.utilisateurs.index', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { name: 'Services & Fournitures', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
];

const logout = () => {
    if (confirm('Déconnexion ?')) router.post(route('logout'));
};
</script>