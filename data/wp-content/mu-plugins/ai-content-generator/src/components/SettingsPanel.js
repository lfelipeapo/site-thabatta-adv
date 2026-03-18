/**
 * Painel de configurações
 *
 * @package AICG
 */

import { useState, useEffect, useCallback } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import {
    Button,
    TextControl,
    SelectControl,
    ToggleControl,
    Notice,
    __experimentalVStack as VStack,
    __experimentalHeading as Heading,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const buildModelOptions = (models, currentModel) => {
    if (Array.isArray(models) && models.length > 0) {
        return models.map((model) => ({
            label: model.name || model.id,
            value: model.id,
        }));
    }

    if (currentModel) {
        return [
            {
                label: currentModel,
                value: currentModel,
            },
        ];
    }

    return [
        {
            label: __('Nenhum modelo disponível', 'ai-content-generator'),
            value: '',
        },
    ];
};

const SettingsPanel = ({ settings, onSave }) => {
    const [formData, setFormData] = useState({
        api_key: '',
        default_model: settings?.default_model || '',
        default_tone: settings?.default_tone || 'professional',
        default_length: settings?.default_length || 'medium',
        include_images: settings?.include_images !== false,
        cache_enabled: settings?.cache_enabled !== false,
        async_generation: settings?.async_generation !== false,
    });
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState(null);
    const [success, setSuccess] = useState(false);
    const [availableModels, setAvailableModels] = useState(settings?.available_models || []);
    const [loadingModels, setLoadingModels] = useState(false);

    const loadModels = useCallback(async (forceRefresh = false, allowManualKey = false) => {
        if (!settings?.api_key_configured && !allowManualKey) {
            return [];
        }

        setLoadingModels(true);

        try {
            const response = await apiFetch({
                path: `aicg/v1/models${forceRefresh ? '?refresh=1' : ''}`,
            });

            if (!response.success) {
                return [];
            }

            const models = Array.isArray(response.data) ? response.data : [];
            setAvailableModels(models);

            setFormData((current) => {
                if (models.length === 0) {
                    return current;
                }

                if (current.default_model && models.some((model) => model.id === current.default_model)) {
                    return current;
                }

                return {
                    ...current,
                    default_model: models[0].id,
                };
            });

            return models;
        } catch (err) {
            setError(err.message || __('Erro ao carregar modelos da Groq.', 'ai-content-generator'));
            return [];
        } finally {
            setLoadingModels(false);
        }
    }, [settings?.api_key_configured]);

    useEffect(() => {
        if (!settings) {
            return;
        }

        setFormData((current) => ({
            ...current,
            default_model: settings.default_model || '',
            default_tone: settings.default_tone || 'professional',
            default_length: settings.default_length || 'medium',
            include_images: settings.include_images !== false,
            cache_enabled: settings.cache_enabled !== false,
            async_generation: settings.async_generation !== false,
        }));
        setAvailableModels(Array.isArray(settings.available_models) ? settings.available_models : []);
    }, [settings]);

    useEffect(() => {
        if (!settings?.api_key_configured) {
            return;
        }

        loadModels(true);
    }, [loadModels, settings?.api_key_configured]);

    const handleSave = async () => {
        setSaving(true);
        setError(null);
        setSuccess(false);

        try {
            const response = await apiFetch({
                path: 'aicg/v1/settings',
                method: 'POST',
                data: formData,
            });

            if (response.success) {
                setSuccess(true);
                onSave?.({
                    ...settings,
                    ...formData,
                    available_models: availableModels,
                });
            } else {
                setError(response.message || __('Erro ao salvar.', 'ai-content-generator'));
            }
        } catch (err) {
            setError(err.message || __('Erro ao salvar.', 'ai-content-generator'));
        } finally {
            setSaving(false);
        }
    };

    const handleValidateApi = async () => {
        if (!formData.api_key) {
            return;
        }

        setSaving(true);
        setError(null);

        try {
            const response = await apiFetch({
                path: 'aicg/v1/validate-api',
                method: 'POST',
                data: { api_key: formData.api_key },
            });

            if (response.success) {
                setSuccess(true);
                await loadModels(true, true);
            } else {
                setError(response.message || __('Chave API inválida.', 'ai-content-generator'));
            }
        } catch (err) {
            setError(err.message || __('Erro ao validar chave.', 'ai-content-generator'));
        } finally {
            setSaving(false);
        }
    };

    const modelOptions = buildModelOptions(availableModels, formData.default_model);

    return (
        <VStack spacing={4} className="aicg-settings-panel">
            {error && (
                <Notice status="error" isDismissible onDismiss={() => setError(null)}>
                    {error}
                </Notice>
            )}

            {success && (
                <Notice status="success" isDismissible onDismiss={() => setSuccess(false)}>
                    {__('Configurações salvas!', 'ai-content-generator')}
                </Notice>
            )}

            {settings?.async_available === false && (
                <Notice status="warning" isDismissible={false}>
                    {__('O WP-Cron está desativado neste ambiente. O plugin vai usar geração síncrona para evitar jobs presos na fila.', 'ai-content-generator')}
                </Notice>
            )}

            <VStack spacing={3}>
                <Heading level={3}>
                    {__('API Groq', 'ai-content-generator')}
                </Heading>

                <TextControl
                    label={__('Chave API', 'ai-content-generator')}
                    type="password"
                    value={formData.api_key}
                    onChange={(api_key) => setFormData({ ...formData, api_key })}
                    placeholder={settings?.api_key_configured ? __('••••••••••••••••', 'ai-content-generator') : ''}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                    help={settings?.api_key_configured 
                        ? __('Chave API já configurada. Deixe em branco para manter.', 'ai-content-generator')
                        : __('Obtenha sua chave em console.groq.com', 'ai-content-generator')
                    }
                />

                {formData.api_key && (
                    <Button
                        variant="secondary"
                        onClick={handleValidateApi}
                        isBusy={saving}
                        disabled={!formData.api_key}
                    >
                        {__('Validar Chave', 'ai-content-generator')}
                    </Button>
                )}

                <SelectControl
                    label={__('Modelo Padrão', 'ai-content-generator')}
                    value={formData.default_model}
                    options={modelOptions}
                    onChange={(default_model) => setFormData({ ...formData, default_model })}
                    disabled={loadingModels || modelOptions[0]?.value === ''}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                    help={
                        loadingModels
                            ? __('Carregando modelos atuais da Groq...', 'ai-content-generator')
                            : (!settings?.api_key_configured && !formData.api_key)
                                ? __('Salve ou valide uma chave API para carregar a lista atual de modelos da Groq.', 'ai-content-generator')
                                : __('Lista carregada diretamente da API de modelos da Groq.', 'ai-content-generator')
                    }
                />
            </VStack>

            <VStack spacing={3}>
                <Heading level={3}>
                    {__('Preferências', 'ai-content-generator')}
                </Heading>

                <SelectControl
                    label={__('Tom de Voz Padrão', 'ai-content-generator')}
                    value={formData.default_tone}
                    options={[
                        { label: __('Profissional', 'ai-content-generator'), value: 'professional' },
                        { label: __('Casual', 'ai-content-generator'), value: 'casual' },
                        { label: __('Técnico', 'ai-content-generator'), value: 'technical' },
                        { label: __('Persuasivo', 'ai-content-generator'), value: 'persuasive' },
                        { label: __('Narrativo', 'ai-content-generator'), value: 'narrative' },
                    ]}
                    onChange={(default_tone) => setFormData({ ...formData, default_tone })}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                />

                <SelectControl
                    label={__('Comprimento Padrão', 'ai-content-generator')}
                    value={formData.default_length}
                    options={[
                        { label: __('Curto (300-500 palavras)', 'ai-content-generator'), value: 'short' },
                        { label: __('Médio (800-1200 palavras)', 'ai-content-generator'), value: 'medium' },
                        { label: __('Longo (1500-2500 palavras)', 'ai-content-generator'), value: 'long' },
                    ]}
                    onChange={(default_length) => setFormData({ ...formData, default_length })}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                />

                <ToggleControl
                    label={__('Incluir imagens destacadas', 'ai-content-generator')}
                    checked={formData.include_images}
                    onChange={(include_images) => setFormData({ ...formData, include_images })}
                    __nextHasNoMarginBottom
                />

                <ToggleControl
                    label={__('Habilitar cache de respostas', 'ai-content-generator')}
                    checked={formData.cache_enabled}
                    onChange={(cache_enabled) => setFormData({ ...formData, cache_enabled })}
                    __nextHasNoMarginBottom
                />

                <ToggleControl
                    label={__('Usar processamento assíncrono', 'ai-content-generator')}
                    checked={formData.async_generation}
                    onChange={(async_generation) => setFormData({ ...formData, async_generation })}
                    disabled={settings?.async_available === false}
                    __nextHasNoMarginBottom
                    help={
                        settings?.async_available === false
                            ? __('O WP-Cron está desligado, então o processamento assíncrono foi desativado para não deixar gerações presas.', 'ai-content-generator')
                            : __('Recomendado para evitar timeouts em gerações longas.', 'ai-content-generator')
                    }
                />
            </VStack>

            <Button
                variant="primary"
                onClick={handleSave}
                isBusy={saving}
                disabled={saving}
            >
                {saving 
                    ? __('Salvando...', 'ai-content-generator')
                    : __('Salvar Configurações', 'ai-content-generator')
                }
            </Button>
        </VStack>
    );
};

export default SettingsPanel;
