/**
 * Ponto de entrada da aplicação React
 *
 * @package AICG
 */

import { createRoot } from '@wordpress/element';
import { useState, useCallback, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { 
    Button, 
    TextareaControl, 
    SelectControl, 
    ToggleControl,
    Panel,
    PanelBody,
    PanelRow,
    Spinner,
    Notice,
    DateTimePicker,
    Modal,
    TabPanel,
    __experimentalText as Text,
    __experimentalHeading as Heading,
    __experimentalVStack as VStack,
    __experimentalHStack as HStack,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

// Components
import PromptForm from './components/PromptForm';
import PreviewPanel from './components/PreviewPanel';
import StatusIndicator from './components/StatusIndicator';
import SettingsPanel from './components/SettingsPanel';
import { useGeneration } from './hooks/useGeneration';
import './styles/app.css';

/**
 * Componente principal da aplicação
 */
const App = () => {
    const [activeTab, setActiveTab] = useState('generate');
    const [settings, setSettings] = useState(null);
    const [showSettings, setShowSettings] = useState(false);

    // Carrega configurações
    useEffect(() => {
        apiFetch({ path: 'aicg/v1/settings' })
            .then((response) => {
                if (response.success) {
                    setSettings(response.data);
                }
            })
            .catch(() => {
                // Fallback
                setSettings({
                    default_tone: 'professional',
                    default_length: 'medium',
                    include_images: true,
                });
            });
    }, []);

    return (
        <div className="aicg-app">
            <header className="aicg-header">
                <HStack alignment="center" justify="space-between">
                    <Heading level={1}>
                        {__('Gerador de Conteúdo IA', 'ai-content-generator')}
                    </Heading>
                    <Button 
                        variant="secondary" 
                        onClick={() => setShowSettings(true)}
                        icon="admin-generic"
                    >
                        {__('Configurações', 'ai-content-generator')}
                    </Button>
                </HStack>
            </header>

            <TabPanel
                className="aicg-tabs"
                activeClass="active-tab"
                onSelect={setActiveTab}
                tabs={[
                    {
                        name: 'generate',
                        title: __('Gerar Conteúdo', 'ai-content-generator'),
                        className: 'tab-generate',
                    },
                    {
                        name: 'history',
                        title: __('Histórico', 'ai-content-generator'),
                        className: 'tab-history',
                    },
                ]}
            >
                {(tab) => (
                    <div className="aicg-tab-content">
                        {tab.name === 'generate' && <GeneratorTab settings={settings} />}
                        {tab.name === 'history' && <HistoryTab />}
                    </div>
                )}
            </TabPanel>

            {showSettings && (
                <Modal
                    title={__('Configurações', 'ai-content-generator')}
                    onRequestClose={() => setShowSettings(false)}
                    className="aicg-settings-modal"
                >
                    <SettingsPanel 
                        settings={settings} 
                        onSave={(newSettings) => {
                            setSettings(newSettings);
                            setShowSettings(false);
                        }}
                    />
                </Modal>
            )}
        </div>
    );
};

/**
 * Aba de geração
 */
const GeneratorTab = ({ settings }) => {
    const { status, result, error, generate, reset } = useGeneration();
    const [showPreview, setShowPreview] = useState(false);

    const handleGenerate = useCallback(async (formData) => {
        const response = await generate(formData);
        
        if (response.success) {
            setShowPreview(true);
        }
    }, [generate]);

    const handleReset = useCallback(() => {
        reset();
        setShowPreview(false);
    }, [reset]);

    if (status === 'completed' && result && showPreview) {
        return (
            <PreviewPanel 
                result={result} 
                onReset={handleReset}
                onEdit={() => {
                    window.open(result.data.edit_link, '_blank');
                }}
            />
        );
    }

    return (
        <div className="aicg-generator">
            {error && (
                <Notice status="error" isDismissible onDismiss={handleReset}>
                    {error}
                </Notice>
            )}

            <div className="aicg-generator-layout">
                <div className="aicg-generator-form">
                    <PromptForm 
                        onSubmit={handleGenerate}
                        isLoading={status === 'generating'}
                        settings={settings}
                    />
                </div>

                <div className="aicg-generator-status">
                    <StatusIndicator status={status} />
                </div>
            </div>
        </div>
    );
};

/**
 * Aba de histórico
 */
const HistoryTab = () => {
    const [history, setHistory] = useState([]);
    const [loading, setLoading] = useState(true);
    const [page, setPage] = useState(1);

    useEffect(() => {
        loadHistory();
    }, [page]);

    const loadHistory = async () => {
        setLoading(true);
        try {
            const response = await apiFetch({
                path: `aicg/v1/history?page=${page}&per_page=10`,
            });
            
            if (response.success) {
                setHistory(response.data.items);
            }
        } catch (err) {
            console.error('Failed to load history:', err);
        }
        setLoading(false);
    };

    if (loading) {
        return <Spinner />;
    }

    return (
        <div className="aicg-history">
            {history.length === 0 ? (
                <Text>
                    {__('Nenhum conteúdo gerado ainda.', 'ai-content-generator')}
                </Text>
            ) : (
                <VStack spacing={4}>
                    {history.map((item) => (
                        <HistoryItem key={item.job_id} item={item} />
                    ))}
                </VStack>
            )}
        </div>
    );
};

/**
 * Item do histórico
 */
const HistoryItem = ({ item }) => {
    const statusLabels = {
        pending: __('Pendente', 'ai-content-generator'),
        processing: __('Processando', 'ai-content-generator'),
        completed: __('Concluído', 'ai-content-generator'),
        failed: __('Falhou', 'ai-content-generator'),
        cancelled: __('Cancelado', 'ai-content-generator'),
    };

    const statusClasses = {
        pending: 'is-pending',
        processing: 'is-processing',
        completed: 'is-completed',
        failed: 'is-failed',
        cancelled: 'is-cancelled',
    };

    return (
        <Panel className={`aicg-history-item ${statusClasses[item.status] || ''}`}>
            <PanelBody title={item.prompt_preview} initialOpen={false}>
                <PanelRow>
                    <Text>
                        <strong>{__('Status:', 'ai-content-generator')}</strong>{' '}
                        {statusLabels[item.status] || item.status}
                    </Text>
                </PanelRow>
                <PanelRow>
                    <Text>
                        <strong>{__('Tipo:', 'ai-content-generator')}</strong>{' '}
                        {item.content_type}
                    </Text>
                </PanelRow>
                {item.post_id && (
                    <PanelRow>
                        <Button
                            variant="secondary"
                            href={item.edit_link}
                            target="_blank"
                        >
                            {__('Editar Post', 'ai-content-generator')}
                        </Button>
                    </PanelRow>
                )}
            </PanelBody>
        </Panel>
    );
};

// Inicializa aplicação
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('aicg-root');
    
    if (container) {
        const root = createRoot(container);
        root.render(<App />);
    }
});
