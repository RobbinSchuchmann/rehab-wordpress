import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Variant" initialOpen>
						<SelectControl
							label="Background"
							value={ a.background }
							options={ [
								{ label: 'Sage (mid-page conversion)', value: 'sage' },
								{ label: 'Dark (closing concierge)', value: 'dark' },
								{ label: 'None (inherit section)', value: 'none' },
							] }
							onChange={ set( 'background' ) }
						/>
						<ToggleControl label="Compact (actions only)" checked={ a.compact } onChange={ set( 'compact' ) } />
					</PanelBody>
					<PanelBody title="Links" initialOpen={ false }>
						<TextControl label="Primary URL" value={ a.primaryUrl } onChange={ set( 'primaryUrl' ) } />
						<TextControl label="Secondary text (e.g. WhatsApp)" value={ a.secondaryText } onChange={ set( 'secondaryText' ) } />
						<TextControl label="Secondary URL" value={ a.secondaryUrl } onChange={ set( 'secondaryUrl' ) } />
						<TextControl label="Phone href" value={ a.phoneHref } onChange={ set( 'phoneHref' ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-cta-band rehab-cta-band--${ a.background }${ a.compact ? ' rehab-cta-band--compact' : '' }` }>
						<div className="rehab-container rehab-container--narrow">
							{ ! a.compact ? (
								<>
									<RichText tagName="p" className="rehab-cta-band__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
									<RichText tagName="h2" className="rehab-cta-band__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
									<RichText tagName="p" className="rehab-cta-band__lede" value={ a.lede } onChange={ set( 'lede' ) } placeholder="Lede…" allowedFormats={ [] } />
								</>
							) : null }
							<div className="rehab-cta-band__actions">
								<span className={ a.background === 'dark' ? 'rehab-btn rehab-btn--luxury' : 'rehab-btn rehab-btn--dark' }>
									<RichText tagName="span" value={ a.primaryText } onChange={ set( 'primaryText' ) } placeholder="Primary CTA…" allowedFormats={ [] } />
								</span>
								{ a.secondaryText ? <span className="rehab-btn rehab-btn--light">{ a.secondaryText }</span> : null }
								<span className="rehab-phone-link">
									<RichText tagName="u" value={ a.phoneText } onChange={ set( 'phoneText' ) } placeholder="Phone…" allowedFormats={ [] } />
								</span>
							</div>
							{ ! a.compact ? (
								<RichText tagName="p" className="rehab-cta-band__helper" value={ a.helper } onChange={ set( 'helper' ) } placeholder="Helper…" allowedFormats={ [] } />
							) : null }
						</div>
					</section>
				</div>
			</>
		);
	},
	save: () => null,
} );
