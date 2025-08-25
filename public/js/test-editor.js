// Test simple pour vérifier le chargement
console.log('Test JavaScript chargé !');

// Test de la classe AdvancedWysiwygEditor
if (typeof AdvancedWysiwygEditor !== 'undefined') {
    console.log('✅ AdvancedWysiwygEditor est disponible');
} else {
    console.log('❌ AdvancedWysiwygEditor n\'est PAS disponible');
}

// Test de création d'instance
try {
    const testEditor = new AdvancedWysiwygEditor('#test', {});
    console.log('✅ Instance créée avec succès:', testEditor);
} catch (error) {
    console.error('❌ Erreur lors de la création:', error);
}
